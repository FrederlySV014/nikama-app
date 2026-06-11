<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DriverLiveLocation;
use App\Models\DriverProfile;
use App\Models\DriverReview;
use App\Models\Order;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the customer's orders.
     */
    public function index(): View
    {
        $user = auth()->user();

        $orders = $user->orders()
            ->with(['items'])
            ->latest()
            ->paginate(10);

        return view('public.orders.index', compact('orders'));
    }

    /**
     * Display the specified order tracking page.
     */
    public function show(Order $order): View
    {
        if (! $this->checkAccess($order, 'view')) {
            abort(403, 'No tienes autorización para ver este pedido.');
        }

        $order->load([
            'items.options.productOption.group',
            'statusHistory.changedByUser',
            'driverReviews',
        ]);

        $canManage = $this->checkAccess($order, 'manage');

        return view('public.orders.show', compact('order', 'canManage'));
    }

    /**
     * Get the real-time location and status of the order.
     */
    public function location(Order $order): JsonResponse
    {
        if (! $this->checkAccess($order, 'view')) {
            abort(403, 'No tienes autorización para ver esta información.');
        }

        $businessLocation = $order->getBusinessLocation();

        // Fallback coordinates if business location is not resolved
        $originLat = $businessLocation ? (float) $businessLocation->latitude : -6.7719;
        $originLng = $businessLocation ? (float) $businessLocation->longitude : -79.8441;
        $originName = $businessLocation ? $businessLocation->name : 'Sede Nikama';

        $destLat = $order->delivery_latitude ? (float) $order->delivery_latitude : -6.7725;
        $destLng = $order->delivery_longitude ? (float) $order->delivery_longitude : -79.8465;
        $destAddress = $order->delivery_address ?? 'Dirección del Cliente';

        $driverLat = $originLat;
        $driverLng = $originLng;
        $percentage = 0.0;
        $elapsedSeconds = 0;
        $totalSeconds = 60; // Total duration of simulation in seconds
        $hasLiveCoords = false;

        // Try to read active driver coordinates if available
        $delivery = $order->deliveries()->where('status', '!=', Delivery::STATUS_FAILED)->first();
        if ($delivery && $delivery->driver_profile_id && $order->status === Order::STATUS_ON_THE_WAY) {
            $liveLoc = DriverLiveLocation::where('driver_profile_id', $delivery->driver_profile_id)->first();
            if ($liveLoc && $liveLoc->latitude && $liveLoc->longitude) {
                $driverLat = (float) $liveLoc->latitude;
                $driverLng = (float) $liveLoc->longitude;
                $hasLiveCoords = true;

                // Calculate percentage based on distance covered relative to total distance
                $totalDist = sqrt(pow($destLat - $originLat, 2) + pow($destLng - $originLng, 2));
                if ($totalDist > 0) {
                    $coveredDist = sqrt(pow($driverLat - $originLat, 2) + pow($driverLng - $originLng, 2));
                    $percentage = min(1.0, $coveredDist / $totalDist);
                }
                $elapsedSeconds = (int) round($percentage * $totalSeconds);
            }
        }

        if (! $hasLiveCoords) {
            if ($order->status === Order::STATUS_ON_THE_WAY) {
                $history = $order->statusHistory()
                    ->where('status', Order::STATUS_ON_THE_WAY)
                    ->latest()
                    ->first();

                $startTime = $history ? $history->created_at : $order->updated_at;
                $elapsedSeconds = now()->diffInSeconds($startTime);
                $percentage = min(1.0, max(0.0, $elapsedSeconds / $totalSeconds));

                // Z-shaped street path interpolation between store (origin) and customer (destination)
                if ($percentage <= 0.3) {
                    $segProgress = $percentage / 0.3;
                    $driverLat = $originLat;
                    $driverLng = $originLng + ($destLng - $originLng) * 0.6 * $segProgress;
                } elseif ($percentage <= 0.75) {
                    $segProgress = ($percentage - 0.3) / 0.45;
                    $driverLat = $originLat + ($destLat - $originLat) * $segProgress;
                    $driverLng = $originLng + ($destLng - $originLng) * 0.6;
                } else {
                    $segProgress = ($percentage - 0.75) / 0.25;
                    $driverLat = $destLat;
                    $driverLng = ($originLng + ($destLng - $originLng) * 0.6) +
                                  ($destLng - ($originLng + ($destLng - $originLng) * 0.6)) * $segProgress;
                }
            } elseif ($order->status === Order::STATUS_DELIVERED) {
                $driverLat = $destLat;
                $driverLng = $destLng;
                $percentage = 1.0;
                $elapsedSeconds = $totalSeconds;
            }
        }

        $latestAssignment = $order->driverAssignments()->latest()->first();
        $assignmentStatus = $latestAssignment ? $latestAssignment->status : null;
        $driverName = $latestAssignment && $latestAssignment->driver && $latestAssignment->driver->user
            ? $latestAssignment->driver->user->first_name.' '.$latestAssignment->driver->user->last_name
            : null;

        return response()->json([
            'status' => $order->status,
            'assignment_status' => $assignmentStatus,
            'driver_name' => $driverName,
            'origin' => [
                'latitude' => $originLat,
                'longitude' => $originLng,
                'name' => $originName,
            ],
            'destination' => [
                'latitude' => $destLat,
                'longitude' => $destLng,
                'address' => $destAddress,
            ],
            'driver' => [
                'latitude' => $driverLat,
                'longitude' => $driverLng,
            ],
            'simulation' => [
                'percentage' => $percentage,
                'elapsed_seconds' => $elapsedSeconds,
                'total_seconds' => $totalSeconds,
            ],
        ]);
    }

    /**
     * Simulate a status transition for testing purposes.
     */
    public function simulateStatus(Order $order, Request $request): JsonResponse|RedirectResponse
    {
        if (! $this->checkAccess($order, 'manage')) {
            abort(403, 'No tienes autorización para realizar esta acción.');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', Order::statuses())],
        ]);

        $newStatus = $request->status;

        DB::transaction(function () use ($order, $newStatus) {
            $order->status = $newStatus;

            if ($newStatus === Order::STATUS_CONFIRMED) {
                $order->confirmed_at = now();
            } elseif ($newStatus === Order::STATUS_DELIVERED) {
                $order->delivered_at = now();
                $order->payment_status = Order::PAYMENT_STATUS_PAID;
            } elseif ($newStatus === Order::STATUS_CANCELLED) {
                $order->cancelled_at = now();
            }

            $order->save();

            // Record status change history
            $order->statusHistory()->create([
                'status' => $newStatus,
                'description' => 'Simulación de estado: '.ucfirst($newStatus),
                'changed_by_user_id' => auth()->id(),
            ]);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'El estado del pedido fue simulado exitosamente a: '.$newStatus,
                'status' => $order->status,
            ]);
        }

        return back()->with('success', 'El estado del pedido fue actualizado a: '.ucfirst($newStatus));
    }

    /**
     * Submit a rating and review for the driver.
     */
    public function rateDriver(Order $order, Request $request): RedirectResponse
    {
        // 1. Ensure user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403, 'No tienes autorización para calificar en este pedido.');
        }

        // 2. Ensure order is delivered
        if ($order->status !== Order::STATUS_DELIVERED) {
            return back()->with('error', 'Solo puedes calificar al repartidor después de recibir tu pedido.');
        }

        // 3. Find the driver associated with this order's deliveries
        $delivery = $order->deliveries()->where('status', Delivery::STATUS_DELIVERED)->first();
        if (! $delivery || ! $delivery->driver_profile_id) {
            return back()->with('error', 'No se encontró un repartidor asignado a este pedido.');
        }

        // 4. Ensure order has not been rated yet
        if (DriverReview::where('order_id', $order->id)->exists()) {
            return back()->with('error', 'Ya has calificado al repartidor de este pedido.');
        }

        // 5. Validate input
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $driverId = $delivery->driver_profile_id;

        DB::transaction(function () use ($order, $driverId, $request) {
            // Create review
            DriverReview::create([
                'driver_profile_id' => $driverId,
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_visible' => true,
            ]);

            // Recalculate driver average rating
            $average = DriverReview::where('driver_profile_id', $driverId)->avg('rating');

            $driverProfile = DriverProfile::findOrFail($driverId);
            $driverProfile->update([
                'rating_average' => (float) $average,
            ]);
        });

        return back()->with('success', '¡Gracias por calificar a tu repartidor!');
    }

    /**
     * Check if the authenticated user has access to view or manage the order.
     */
    private function checkAccess(Order $order, string $action = 'view'): bool
    {
        $user = auth()->user();

        // 1. Super Admin is always allowed
        if ($user->hasRole(Role::SUPER_ADMIN)) {
            return true;
        }

        // 2. Customer who placed the order can only view, not manage
        if ($order->user_id === $user->id) {
            return $action === 'view';
        }

        // 3. Sellers associated with the business can view and manage status
        $firstItem = $order->items()->first();
        if ($firstItem && $firstItem->business_id) {
            $isSellerForBusiness = DB::table('business_users')
                ->where('business_id', $firstItem->business_id)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->exists();

            if ($isSellerForBusiness && $user->hasRole(Role::SELLER)) {
                return true;
            }
        }

        return false;
    }
}
