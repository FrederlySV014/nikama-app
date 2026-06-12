<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DriverAssignmentResource;
use App\Models\Delivery;
use App\Models\DriverAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverDashboardController extends Controller
{
    /**
     * Get dashboard data for the authenticated driver.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->driverProfile;

        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un perfil de repartidor registrado.',
            ], 403);
        }

        // Check driver status
        if ($profile->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Tu perfil de repartidor está en estado: '.$profile->status.'.',
                'status' => $profile->status,
            ], 403);
        }

        // Pending assignments (assigned status)
        $pendingAssignments = DriverAssignment::where('driver_profile_id', $profile->id)
            ->where('status', DriverAssignment::STATUS_ASSIGNED)
            ->with(['order.items', 'delivery.business.locations'])
            ->latest()
            ->get();

        // Active delivery in progress (assigned, picked_up, or on_the_way status)
        $activeDelivery = Delivery::where('driver_profile_id', $profile->id)
            ->whereIn('status', [Delivery::STATUS_ASSIGNED, Delivery::STATUS_PICKED_UP, Delivery::STATUS_ON_THE_WAY])
            ->with(['order.items', 'business.locations'])
            ->first();

        // Today's completed and failed counts
        $todayCompletedCount = Delivery::where('driver_profile_id', $profile->id)
            ->where('status', Delivery::STATUS_DELIVERED)
            ->whereDate('delivered_at', today())
            ->count();

        $todayFailedCount = Delivery::where('driver_profile_id', $profile->id)
            ->where('status', Delivery::STATUS_FAILED)
            ->whereDate('failed_at', today())
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'today_completed_count' => $todayCompletedCount,
                'today_failed_count' => $todayFailedCount,
                'active_delivery' => $activeDelivery ? [
                    'id' => $activeDelivery->id,
                    'status' => $activeDelivery->status,
                    'picked_up_at' => $activeDelivery->picked_up_at ? $activeDelivery->picked_up_at->toIso8601String() : null,
                    'delivered_at' => $activeDelivery->delivered_at ? $activeDelivery->delivered_at->toIso8601String() : null,
                    'failed_at' => $activeDelivery->failed_at ? $activeDelivery->failed_at->toIso8601String() : null,
                    'business' => [
                        'id' => $activeDelivery->business->id,
                        'business_name' => $activeDelivery->business->business_name,
                        'address' => $activeDelivery->business->locations->first() ? $activeDelivery->business->locations->first()->address : null,
                        'latitude' => $activeDelivery->business->locations->first() ? (float) $activeDelivery->business->locations->first()->latitude : null,
                        'longitude' => $activeDelivery->business->locations->first() ? (float) $activeDelivery->business->locations->first()->longitude : null,
                    ],
                    'order' => [
                        'id' => $activeDelivery->order->id,
                        'order_number' => $activeDelivery->order->order_number,
                        'total' => $activeDelivery->order->total,
                        'delivery_address' => $activeDelivery->order->delivery_address,
                        'delivery_reference' => $activeDelivery->order->delivery_reference,
                        'delivery_latitude' => $activeDelivery->order->delivery_latitude,
                        'delivery_longitude' => $activeDelivery->order->delivery_longitude,
                        'notes' => $activeDelivery->order->notes,
                        'items' => $activeDelivery->order->items->map(fn ($item) => [
                            'product_name' => $item->product_name,
                            'quantity' => $item->quantity,
                        ]),
                    ],
                ] : null,
                'pending_assignments' => DriverAssignmentResource::collection($pendingAssignments),
            ],
        ]);
    }
}
