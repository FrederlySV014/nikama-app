<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DriverAssignment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard de Vendedores.
     */
    public function seller(): View
    {
        $user = auth()->user();
        $businessIds = $user->businesses()->pluck('businesses.id')->toArray();

        // 1. Pedidos Hoy
        $todayOrdersCount = Order::whereHas('items', function ($query) use ($businessIds) {
            $query->whereIn('business_id', $businessIds);
        })
            ->whereDate('created_at', today())
            ->count();

        // 2. Productos Activos
        $activeProductsCount = Product::whereIn('business_id', $businessIds)
            ->where('status', Product::STATUS_ACTIVE)
            ->count();

        // 3. Ventas de la Semana
        $weeklySales = OrderItem::whereIn('business_id', $businessIds)
            ->whereHas('order', function ($query) {
                $query->where('status', '!=', Order::STATUS_CANCELLED)
                    ->where('created_at', '>=', now()->startOfWeek());
            })
            ->sum('subtotal');

        // 4. Ventas de Hoy
        $todaySales = OrderItem::whereIn('business_id', $businessIds)
            ->whereHas('order', function ($query) {
                $query->where('status', '!=', Order::STATUS_CANCELLED)
                    ->whereDate('created_at', today());
            })
            ->sum('subtotal');

        // 5. Pedidos Pendientes (Atención Urgente)
        $pendingOrdersCount = Order::whereHas('items', function ($query) use ($businessIds) {
            $query->whereIn('business_id', $businessIds);
        })
            ->where('status', Order::STATUS_PENDING)
            ->count();

        // 6. Pedidos En Preparación o En Camino
        $activeDeliveriesCount = Order::whereHas('items', function ($query) use ($businessIds) {
            $query->whereIn('business_id', $businessIds);
        })
            ->whereIn('status', [Order::STATUS_PREPARING, Order::STATUS_ON_THE_WAY])
            ->count();

        // 7. Últimos 5 Pedidos
        $recentOrders = Order::whereHas('items', function ($query) use ($businessIds) {
            $query->whereIn('business_id', $businessIds);
        })
            ->with(['items', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // 8. Productos más vendidos (Top 5)
        $topProducts = OrderItem::whereIn('business_id', $businessIds)
            ->select('product_name', \DB::raw('SUM(quantity) as total_qty'), \DB::raw('SUM(subtotal) as total_rev'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('seller.dashboard', compact(
            'todayOrdersCount',
            'activeProductsCount',
            'weeklySales',
            'todaySales',
            'pendingOrdersCount',
            'activeDeliveriesCount',
            'recentOrders',
            'topProducts'
        ));
    }

    /**
     * Dashboard de Repartidores.
     */
    public function driver(): View
    {
        $user = Auth::user();
        $profile = $user->driverProfile;

        // Query pending assignments (assigned status) for this driver
        $pendingAssignments = $profile
            ? DriverAssignment::where('driver_profile_id', $profile->id)
                ->where('status', DriverAssignment::STATUS_ASSIGNED)
                ->with(['order.items', 'delivery.business'])
                ->latest()
                ->get()
            : collect();

        // Query active delivery in progress (assigned, picked_up, or on_the_way status)
        $activeDelivery = $profile
            ? Delivery::where('driver_profile_id', $profile->id)
                ->whereIn('status', [Delivery::STATUS_ASSIGNED, Delivery::STATUS_PICKED_UP, Delivery::STATUS_ON_THE_WAY])
                ->with(['order.items', 'business.locations'])
                ->first()
            : null;

        // Today's completed and failed counts
        $todayCompletedCount = $profile
            ? Delivery::where('driver_profile_id', $profile->id)
                ->where('status', Delivery::STATUS_DELIVERED)
                ->whereDate('delivered_at', today())
                ->count()
            : 0;

        $todayFailedCount = $profile
            ? Delivery::where('driver_profile_id', $profile->id)
                ->where('status', Delivery::STATUS_FAILED)
                ->whereDate('failed_at', today())
                ->count()
            : 0;

        return view('driver.dashboard', compact('pendingAssignments', 'activeDelivery', 'todayCompletedCount', 'todayFailedCount'));
    }

    /**
     * Dashboard de Administración.
     */
    public function admin(): View
    {
        return view('admin.dashboard');
    }

    /**
     * Mostrar página de revisión pendiente / rechazo / suspensión.
     */
    public function pendingReview(Request $request): View
    {
        $user = Auth::user();
        $status = 'none';
        $reason = null;
        $role = 'none';
        $name = '';

        if ($user instanceof User) {
            if ($user->hasRole(Role::SELLER)) {
                $role = 'seller';
                $business = $user->businesses()->first();
                if ($business) {
                    $status = $business->status;
                    $reason = $business->rejected_reason;
                    $name = $business->business_name;
                }
            } elseif ($user->hasRole(Role::DRIVER)) {
                $role = 'driver';
                $driverProfile = $user->driverProfile;
                if ($driverProfile) {
                    $status = $driverProfile->status;
                    $reason = $driverProfile->rejected_reason;
                    $name = $user->first_name.' '.$user->last_name;
                }
            }
        }

        return view('auth.pending-review', compact('status', 'reason', 'role', 'name'));
    }

    /**
     * Get the count of pending orders for the seller's businesses.
     */
    public function pendingCount(): JsonResponse
    {
        $user = auth()->user();
        if (! $user->hasRole(Role::SELLER)) {
            return response()->json(['count' => 0]);
        }

        $businessIds = $user->businesses()->pluck('businesses.id')->toArray();

        $count = Order::whereHas('items', function ($query) use ($businessIds) {
            $query->whereIn('business_id', $businessIds);
        })
            ->where('status', Order::STATUS_PENDING)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get the count of pending driver assignments (status = assigned) for the driver.
     */
    public function driverPendingCount(): JsonResponse
    {
        $user = auth()->user();
        if (! $user->hasRole(Role::DRIVER)) {
            return response()->json(['count' => 0, 'assignments' => []]);
        }

        $profile = $user->driverProfile;
        if (! $profile) {
            return response()->json(['count' => 0, 'assignments' => []]);
        }

        $pendingAssignments = DriverAssignment::where('driver_profile_id', $profile->id)
            ->where('status', DriverAssignment::STATUS_ASSIGNED)
            ->with(['order:id,order_number,total,delivery_address', 'delivery.business:id,business_name'])
            ->latest()
            ->get();

        return response()->json([
            'count' => $pendingAssignments->count(),
            'assignments' => $pendingAssignments->map(fn ($a) => [
                'id' => $a->id,
                'order_number' => $a->order->order_number ?? 'N/A',
                'delivery_address' => $a->order->delivery_address ?? '',
                'total' => $a->order->total ?? 0,
                'business_name' => $a->delivery->business->business_name ?? 'Negocio',
            ]),
        ]);
    }
}
