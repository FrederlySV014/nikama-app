<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DriverAssignment;
use App\Models\DriverLiveLocation;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderRefund;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DriverOrderController extends Controller
{
    /**
     * Accept a pending driver assignment.
     */
    public function acceptAssignment(Request $request, string $id): JsonResponse
    {
        $profile = $request->user()->driverProfile;
        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un perfil de repartidor registrado.',
            ], 403);
        }

        $assignment = DriverAssignment::where('id', $id)
            ->where('driver_profile_id', $profile->id)
            ->first();

        if (! $assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Asignación no encontrada.',
            ], 404);
        }

        if ($assignment->status !== DriverAssignment::STATUS_ASSIGNED) {
            return response()->json([
                'success' => false,
                'message' => 'Esta asignación ya no está pendiente.',
            ], 400);
        }

        DB::transaction(function () use ($assignment, $request) {
            // 1. Update assignment status to accepted
            $assignment->update([
                'status' => DriverAssignment::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ]);

            // 2. Update delivery status to assigned
            $assignment->delivery->update([
                'status' => Delivery::STATUS_ASSIGNED,
            ]);

            // 3. Log status change history
            $driverName = $request->user()->first_name.' '.$request->user()->last_name;
            $assignment->order->statusHistory()->create([
                'status' => $assignment->order->status,
                'description' => "Repartidor {$driverName} aceptó el pedido para reparto.",
                'changed_by_user_id' => $request->user()->id,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pedido aceptado correctamente.',
        ]);
    }

    /**
     * Reject a pending driver assignment.
     */
    public function rejectAssignment(Request $request, string $id): JsonResponse
    {
        $profile = $request->user()->driverProfile;
        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un perfil de repartidor registrado.',
            ], 403);
        }

        $assignment = DriverAssignment::where('id', $id)
            ->where('driver_profile_id', $profile->id)
            ->first();

        if (! $assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Asignación no encontrada.',
            ], 404);
        }

        if ($assignment->status !== DriverAssignment::STATUS_ASSIGNED) {
            return response()->json([
                'success' => false,
                'message' => 'Esta asignación ya no está pendiente.',
            ], 400);
        }

        DB::transaction(function () use ($assignment, $request) {
            // 1. Update assignment status to rejected
            $assignment->update([
                'status' => DriverAssignment::STATUS_REJECTED,
            ]);

            // 2. Detach driver from delivery and set status to pending
            $assignment->delivery->update([
                'driver_profile_id' => null,
                'status' => Delivery::STATUS_PENDING,
            ]);

            // 3. Log status change history
            $driverName = $request->user()->first_name.' '.$request->user()->last_name;
            $assignment->order->statusHistory()->create([
                'status' => $assignment->order->status,
                'description' => "Repartidor {$driverName} rechazó la solicitud. Reasignando repartidor.",
                'changed_by_user_id' => $request->user()->id,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Asignación rechazada correctamente.',
        ]);
    }

    /**
     * Emit driver coordinates to update driver_live_locations in real-time.
     */
    public function emitLocation(Request $request, string $deliveryId): JsonResponse
    {
        $profile = $request->user()->driverProfile;
        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un perfil de repartidor registrado.',
            ], 403);
        }

        $delivery = Delivery::where('id', $deliveryId)
            ->where('driver_profile_id', $profile->id)
            ->first();

        if (! $delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Despacho no encontrado.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        DB::transaction(function () use ($delivery, $profile, $latitude, $longitude, $request) {
            // 1. Update active coordinates
            DriverLiveLocation::updateOrCreate(
                ['driver_profile_id' => $profile->id],
                [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'is_online' => true,
                    'is_available' => false,
                    'last_location_updated_at' => now(),
                ]
            );

            // 2. If order is not on_the_way, dispatch/start route
            if ($delivery->order->status !== Order::STATUS_ON_THE_WAY) {
                $delivery->order->update([
                    'status' => Order::STATUS_ON_THE_WAY,
                ]);

                $delivery->update([
                    'status' => Delivery::STATUS_ON_THE_WAY,
                    'picked_up_at' => now(),
                ]);

                $driverName = $request->user()->first_name.' '.$request->user()->last_name;
                $delivery->order->statusHistory()->create([
                    'status' => Order::STATUS_ON_THE_WAY,
                    'description' => "Pedido despachado. Repartidor {$driverName} en camino.",
                    'changed_by_user_id' => $request->user()->id,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Ubicación emitida correctamente.',
        ]);
    }

    /**
     * Mark the active delivery as successfully completed/delivered.
     */
    public function completeDelivery(Request $request, string $deliveryId): JsonResponse
    {
        $profile = $request->user()->driverProfile;
        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un perfil de repartidor registrado.',
            ], 403);
        }

        $delivery = Delivery::where('id', $deliveryId)
            ->where('driver_profile_id', $profile->id)
            ->first();

        if (! $delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Despacho no encontrado.',
            ], 404);
        }

        DB::transaction(function () use ($delivery, $profile, $request) {
            // 1. Complete order
            $delivery->order->update([
                'status' => Order::STATUS_DELIVERED,
                'delivered_at' => now(),
                'payment_status' => Order::PAYMENT_STATUS_PAID,
            ]);

            // 2. Complete delivery
            $delivery->update([
                'status' => Delivery::STATUS_DELIVERED,
                'delivered_at' => now(),
            ]);

            // 3. Complete driver assignment
            DriverAssignment::where('order_id', $delivery->order_id)
                ->where('driver_profile_id', $profile->id)
                ->update([
                    'status' => DriverAssignment::STATUS_COMPLETED,
                    'completed_at' => now(),
                ]);

            // 4. Log status change history
            $driverName = $request->user()->first_name.' '.$request->user()->last_name;
            $delivery->order->statusHistory()->create([
                'status' => Order::STATUS_DELIVERED,
                'description' => "Pedido entregado con éxito por: {$driverName}.",
                'changed_by_user_id' => $request->user()->id,
            ]);

            // 5. Update driver stats
            $profile->increment('total_deliveries');

            // Set driver coordinates to final destination and make available again
            DriverLiveLocation::updateOrCreate(
                ['driver_profile_id' => $profile->id],
                [
                    'latitude' => $delivery->order->delivery_latitude,
                    'longitude' => $delivery->order->delivery_longitude,
                    'is_online' => true,
                    'is_available' => true,
                    'last_location_updated_at' => now(),
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => '¡Pedido completado y entregado con éxito!',
        ]);
    }

    /**
     * Mark the active delivery as rejected by the customer.
     */
    public function clientReject(Request $request, string $deliveryId): JsonResponse
    {
        $profile = $request->user()->driverProfile;
        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un perfil de repartidor registrado.',
            ], 403);
        }

        $delivery = Delivery::where('id', $deliveryId)
            ->where('driver_profile_id', $profile->id)
            ->first();

        if (! $delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Despacho no encontrado.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::transaction(function () use ($delivery, $profile, $request) {
            // 1. Cancel order
            $delivery->order->update([
                'status' => Order::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

            // 2. Mark delivery as failed
            $delivery->update([
                'status' => Delivery::STATUS_FAILED,
                'failed_at' => now(),
            ]);

            // 3. Complete driver assignment (close it)
            DriverAssignment::where('order_id', $delivery->order_id)
                ->where('driver_profile_id', $profile->id)
                ->update([
                    'status' => DriverAssignment::STATUS_COMPLETED,
                    'completed_at' => now(),
                ]);

            // 4. Save cancellation log (cancelled_by_type = customer)
            $delivery->order->cancellation()->create([
                'cancelled_by_type' => OrderCancellation::BY_CUSTOMER,
                'cancelled_by_id' => $request->user()->id,
                'reason_code' => 'client_rejected',
                'comment' => $request->rejection_reason,
                'penalty_applied' => false,
                'penalty_amount' => 0.00,
            ]);

            // 5. Check for digital refund
            $payment = $delivery->order->payments()
                ->where('status', Payment::STATUS_PAID)
                ->first();

            if ($payment && $payment->payment_method !== Payment::METHOD_CASH) {
                $delivery->order->refunds()->create([
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                    'status' => OrderRefund::STATUS_PROCESSED,
                    'reason' => $request->rejection_reason,
                    'gateway_refund_id' => 'REF-'.strtoupper(Str::random(10)),
                    'processed_at' => now(),
                ]);

                $payment->update([
                    'status' => Payment::STATUS_REFUNDED,
                    'refunded_at' => now(),
                ]);

                $delivery->order->update([
                    'payment_status' => Order::PAYMENT_STATUS_REFUNDED,
                ]);
            }

            // 6. Log status change history
            $driverName = $request->user()->first_name.' '.$request->user()->last_name;
            $delivery->order->statusHistory()->create([
                'status' => Order::STATUS_CANCELLED,
                'description' => "Pedido rechazado por el cliente al llegar. Registrado por repartidor: {$driverName}. Motivo: ".$request->rejection_reason,
                'changed_by_user_id' => $request->user()->id,
            ]);

            // 7. Make driver available again
            DriverLiveLocation::updateOrCreate(
                ['driver_profile_id' => $profile->id],
                [
                    'latitude' => $delivery->order->delivery_latitude,
                    'longitude' => $delivery->order->delivery_longitude,
                    'is_online' => true,
                    'is_available' => true,
                    'last_location_updated_at' => now(),
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'El pedido fue marcado como Rechazado por el Cliente.',
        ]);
    }

    /**
     * Retrieve a history of deliveries performed by the driver.
     */
    public function history(Request $request): JsonResponse
    {
        $profile = $request->user()->driverProfile;
        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un perfil de repartidor registrado.',
            ], 403);
        }

        $deliveries = Delivery::where('driver_profile_id', $profile->id)
            ->with(['order.items', 'business'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $deliveries->map(fn ($del) => [
                'id' => $del->id,
                'status' => $del->status,
                'delivered_at' => $del->delivered_at ? $del->delivered_at->toIso8601String() : null,
                'failed_at' => $del->failed_at ? $del->failed_at->toIso8601String() : null,
                'business_name' => $del->business ? $del->business->business_name : null,
                'order' => [
                    'id' => $del->order->id,
                    'order_number' => $del->order->order_number,
                    'total' => $del->order->total,
                ],
            ]),
            'pagination' => [
                'total' => $deliveries->total(),
                'per_page' => $deliveries->perPage(),
                'current_page' => $deliveries->currentPage(),
                'last_page' => $deliveries->lastPage(),
            ],
        ]);
    }
}
