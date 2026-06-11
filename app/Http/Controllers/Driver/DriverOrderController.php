<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DriverAssignment;
use App\Models\DriverLiveLocation;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderRefund;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DriverOrderController extends Controller
{
    /**
     * Accept a pending driver assignment.
     */
    public function acceptAssignment(DriverAssignment $assignment): RedirectResponse
    {
        if (! $this->checkDriverAccess($assignment)) {
            abort(403, 'No tienes autorización para aceptar este pedido.');
        }

        DB::transaction(function () use ($assignment) {
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
            $driverName = auth()->user()->first_name.' '.auth()->user()->last_name;
            $assignment->order->statusHistory()->create([
                'status' => $assignment->order->status,
                'description' => "Repartidor {$driverName} aceptó el pedido para reparto.",
                'changed_by_user_id' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Pedido aceptado. Ve a la pantalla de reparto para iniciar el despacho.');
    }

    /**
     * Reject a pending driver assignment.
     */
    public function rejectAssignment(DriverAssignment $assignment): RedirectResponse
    {
        if (! $this->checkDriverAccess($assignment)) {
            abort(403, 'No tienes autorización para rechazar este pedido.');
        }

        DB::transaction(function () use ($assignment) {
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
            $driverName = auth()->user()->first_name.' '.auth()->user()->last_name;
            $assignment->order->statusHistory()->create([
                'status' => $assignment->order->status,
                'description' => "Repartidor {$driverName} rechazó la solicitud. Reasignando repartidor.",
                'changed_by_user_id' => auth()->id(),
            ]);
        });

        return back()->with('warning', 'Has rechazado la solicitud de despacho.');
    }

    /**
     * Display the specified active delivery view.
     */
    public function showDelivery(Delivery $delivery): View
    {
        $profile = auth()->user()->driverProfile;
        if (! $profile || $delivery->driver_profile_id !== $profile->id) {
            abort(403, 'No tienes autorización para gestionar este reparto.');
        }

        $delivery->load(['order.items', 'business.locations']);

        return view('driver.deliveries.show', compact('delivery'));
    }

    /**
     * Emit driver coordinates to update driver_live_locations table in real-time.
     */
    public function emitLocation(Delivery $delivery, Request $request): JsonResponse
    {
        $profile = auth()->user()->driverProfile;
        if (! $profile || $delivery->driver_profile_id !== $profile->id) {
            abort(403, 'No tienes autorización para emitir ubicación en este reparto.');
        }

        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        DB::transaction(function () use ($delivery, $profile, $latitude, $longitude) {
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

                $driverName = auth()->user()->first_name.' '.auth()->user()->last_name;
                $delivery->order->statusHistory()->create([
                    'status' => Order::STATUS_ON_THE_WAY,
                    'description' => "Pedido despachado. Repartidor {$driverName} en camino.",
                    'changed_by_user_id' => auth()->id(),
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
    public function completeDelivery(Delivery $delivery): RedirectResponse
    {
        $profile = auth()->user()->driverProfile;
        if (! $profile || $delivery->driver_profile_id !== $profile->id) {
            abort(403, 'No tienes autorización para completar este reparto.');
        }

        DB::transaction(function () use ($delivery, $profile) {
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
            $driverName = auth()->user()->first_name.' '.auth()->user()->last_name;
            $delivery->order->statusHistory()->create([
                'status' => Order::STATUS_DELIVERED,
                'description' => "Pedido entregado con éxito por: {$driverName}.",
                'changed_by_user_id' => auth()->id(),
            ]);

            // 5. Update driver stats
            $profile->increment('total_deliveries');

            // Set driver coordinates to final destination
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

        return redirect()->route('driver.dashboard')
            ->with('success', '¡Pedido entregado con éxito! Gran trabajo.');
    }

    /**
     * Mark the active delivery as rejected by the customer.
     */
    public function clientReject(Delivery $delivery, Request $request): RedirectResponse
    {
        $profile = auth()->user()->driverProfile;
        if (! $profile || $delivery->driver_profile_id !== $profile->id) {
            abort(403, 'No tienes autorización para gestionar este reparto.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

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
                'cancelled_by_id' => auth()->id(),
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
            $driverName = auth()->user()->first_name.' '.auth()->user()->last_name;
            $delivery->order->statusHistory()->create([
                'status' => Order::STATUS_CANCELLED,
                'description' => "Pedido rechazado por el cliente al llegar. Registrado por repartidor: {$driverName}. Motivo: ".$request->rejection_reason,
                'changed_by_user_id' => auth()->id(),
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

        return redirect()->route('driver.dashboard')
            ->with('warning', 'El pedido fue marcado como Rechazado por el Cliente.');
    }

    /**
     * Display a history of deliveries performed by this driver.
     */
    public function history(): View
    {
        $profile = auth()->user()->driverProfile;
        if (! $profile) {
            abort(403, 'No tienes un perfil de repartidor registrado.');
        }

        $deliveries = Delivery::where('driver_profile_id', $profile->id)
            ->with(['order.items', 'business'])
            ->latest()
            ->paginate(15);

        return view('driver.history', compact('deliveries'));
    }

    /**
     * Check if the authenticated driver has access to the assignment.
     */
    private function checkDriverAccess(DriverAssignment $assignment): bool
    {
        $profile = auth()->user()->driverProfile;

        return $profile && $assignment->driver_profile_id === $profile->id;
    }
}
