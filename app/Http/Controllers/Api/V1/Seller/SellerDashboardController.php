<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerDashboardController extends Controller
{
    /**
     * Get dashboard statistics for the seller.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
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

        // 3. Ventas de Hoy
        $todaySales = OrderItem::whereIn('business_id', $businessIds)
            ->whereHas('order', function ($query) {
                $query->where('status', '!=', Order::STATUS_CANCELLED)
                    ->whereDate('created_at', today());
            })
            ->sum('subtotal');

        // 4. Pedidos Pendientes
        $pendingOrdersCount = Order::whereHas('items', function ($query) use ($businessIds) {
            $query->whereIn('business_id', $businessIds);
        })
            ->where('status', Order::STATUS_PENDING)
            ->count();

        // 5. Últimos Pedidos
        $recentOrders = Order::whereHas('items', function ($query) use ($businessIds) {
            $query->whereIn('business_id', $businessIds);
        })
            ->with(['items.product', 'user:id,first_name,last_name,email'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'today_orders_count' => $todayOrdersCount,
                'active_products_count' => $activeProductsCount,
                'today_sales' => $todaySales,
                'pending_orders_count' => $pendingOrdersCount,
                'recent_orders' => $recentOrders,
            ],
        ]);
    }

    /**
     * Get list of all orders for the seller.
     */
    public function orders(Request $request): JsonResponse
    {
        $user = $request->user();
        $businessIds = $user->businesses()->pluck('businesses.id')->toArray();

        $status = $request->query('status');

        $query = Order::whereHas('items', function ($q) use ($businessIds) {
            $q->whereIn('business_id', $businessIds);
        })->with(['items.product', 'user:id,first_name,last_name']);

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }
}
