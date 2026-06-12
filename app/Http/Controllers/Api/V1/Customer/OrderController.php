<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderResource;
use App\Models\Delivery;
use App\Models\DriverLiveLocation;
use App\Models\DriverProfile;
use App\Models\DriverReview;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the customer's orders.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with(['items.options', 'payments', 'deliveries.driverProfile.user'])
            ->latest()
            ->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    /**
     * Display the specified order details.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['items.options.productOption.group', 'statusHistory.changedByUser', 'payments', 'deliveries.driverProfile.user'])
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * Get real-time location and status of the order.
     */
    public function track(Request $request, string $id): JsonResponse
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado.',
            ], 404);
        }

        $businessLocation = $order->getBusinessLocation();

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
        $totalSeconds = 60;
        $hasLiveCoords = false;

        $delivery = $order->deliveries()->where('status', '!=', Delivery::STATUS_FAILED)->first();
        if ($delivery && $delivery->driver_profile_id && $order->status === Order::STATUS_ON_THE_WAY) {
            $liveLoc = DriverLiveLocation::where('driver_profile_id', $delivery->driver_profile_id)->first();
            if ($liveLoc && $liveLoc->latitude && $liveLoc->longitude) {
                $driverLat = (float) $liveLoc->latitude;
                $driverLng = (float) $liveLoc->longitude;
                $hasLiveCoords = true;

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
            'success' => true,
            'order_status' => $order->status,
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
     * Submit a rating and review for the driver.
     */
    public function rateDriver(Request $request, string $id): JsonResponse
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado.',
            ], 404);
        }

        if ($order->status !== Order::STATUS_DELIVERED) {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes calificar al repartidor después de recibir tu pedido.',
            ], 400);
        }

        $delivery = $order->deliveries()->where('status', Delivery::STATUS_DELIVERED)->first();
        if (! $delivery || ! $delivery->driver_profile_id) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un repartidor asignado a este pedido.',
            ], 400);
        }

        if (DriverReview::where('order_id', $order->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has calificado al repartidor de este pedido.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $driverId = $delivery->driver_profile_id;

        DB::transaction(function () use ($order, $driverId, $request) {
            DriverReview::create([
                'driver_profile_id' => $driverId,
                'user_id' => $request->user()->id,
                'order_id' => $order->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_visible' => true,
            ]);

            $average = DriverReview::where('driver_profile_id', $driverId)->avg('rating');

            $driverProfile = DriverProfile::findOrFail($driverId);
            $driverProfile->update([
                'rating_average' => (float) $average,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => '¡Gracias por calificar a tu repartidor!',
        ]);
    }
}
