<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DriverAssignment;
use App\Models\DriverLiveLocation;
use App\Models\DriverProfile;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderRefund;
use App\Models\Payment;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerOrderController extends Controller
{
    /**
     * Display a listing of the seller's business orders.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get the IDs of the businesses owned by the seller
        $businessIds = $user->businesses()->pluck('businesses.id')->toArray();

        // Retrieve orders containing items from those businesses
        $orders = Order::whereHas('items', function ($query) use ($businessIds) {
            $query->whereIn('business_id', $businessIds);
        })
            ->with(['items'])
            ->latest()
            ->paginate(15);

        return view('seller.orders.index', compact('orders'));
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order): View
    {
        if (! $this->checkSellerAccess($order)) {
            abort(403, 'No tienes autorización para gestionar este pedido.');
        }

        $order->load([
            'items.options.productOption.group',
            'statusHistory.changedByUser',
            'deliveries.driver.user',
            'driverAssignments.driver.user',
        ]);

        // Query active driver profiles to select from (only those not busy with another active delivery/assignment)
        $drivers = DriverProfile::where('status', DriverProfile::STATUS_ACTIVE)
            ->whereDoesntHave('assignments', function ($query) {
                $query->whereIn('status', [DriverAssignment::STATUS_ASSIGNED, DriverAssignment::STATUS_ACCEPTED]);
            })
            ->with('user')
            ->get();

        return view('seller.orders.show', compact('order', 'drivers'));
    }

    /**
     * Update the order status from the seller panel.
     */
    public function updateStatus(Order $order, Request $request): RedirectResponse
    {
        if (! $this->checkSellerAccess($order)) {
            abort(403, 'No tienes autorización para gestionar este pedido.');
        }

        $rules = [
            'status' => ['required', 'string', 'in:'.implode(',', Order::statuses())],
        ];

        if ($request->status === Order::STATUS_CANCELLED) {
            $rules['cancellation_reason'] = ['required', 'string', 'min:5', 'max:500'];
        }

        $request->validate($rules);

        $newStatus = $request->status;

        DB::transaction(function () use ($order, $newStatus, $request) {
            $order->status = $newStatus;

            if ($newStatus === Order::STATUS_CONFIRMED) {
                $order->confirmed_at = now();
            } elseif ($newStatus === Order::STATUS_DELIVERED) {
                $order->delivered_at = now();
                $order->payment_status = Order::PAYMENT_STATUS_PAID;
            } elseif ($newStatus === Order::STATUS_CANCELLED) {
                $order->cancelled_at = now();

                // Save cancellation reason
                $order->cancellation()->create([
                    'cancelled_by_type' => OrderCancellation::BY_BUSINESS,
                    'cancelled_by_id' => auth()->id(),
                    'reason_code' => 'business_cancelled',
                    'comment' => $request->cancellation_reason,
                    'penalty_applied' => false,
                    'penalty_amount' => 0.00,
                ]);

                // Check for digital refund
                $payment = $order->payments()
                    ->where('status', Payment::STATUS_PAID)
                    ->first();

                if ($payment && $payment->payment_method !== Payment::METHOD_CASH) {
                    $order->refunds()->create([
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                        'status' => OrderRefund::STATUS_PROCESSED,
                        'reason' => $request->cancellation_reason,
                        'gateway_refund_id' => 'REF-'.strtoupper(Str::random(10)),
                        'processed_at' => now(),
                    ]);

                    $payment->update([
                        'status' => Payment::STATUS_REFUNDED,
                        'refunded_at' => now(),
                    ]);

                    $order->payment_status = Order::PAYMENT_STATUS_REFUNDED;
                }

                // Fail any active delivery and close any driver assignment
                $activeDeliveries = $order->deliveries()->whereNotIn('status', [Delivery::STATUS_DELIVERED, Delivery::STATUS_FAILED])->get();
                foreach ($activeDeliveries as $activeDelivery) {
                    $activeDelivery->update([
                        'status' => Delivery::STATUS_FAILED,
                        'failed_at' => now(),
                    ]);
                    if ($activeDelivery->driver_profile_id) {
                        DriverLiveLocation::updateOrCreate(
                            ['driver_profile_id' => $activeDelivery->driver_profile_id],
                            [
                                'is_online' => true,
                                'is_available' => true,
                                'last_location_updated_at' => now(),
                            ]
                        );
                    }
                }

                $order->driverAssignments()->whereNotIn('status', [DriverAssignment::STATUS_COMPLETED, DriverAssignment::STATUS_REJECTED])
                    ->update([
                        'status' => DriverAssignment::STATUS_REJECTED,
                        'completed_at' => now(),
                    ]);
            }

            $order->save();

            // Record status change history
            $order->statusHistory()->create([
                'status' => $newStatus,
                'description' => $newStatus === Order::STATUS_CANCELLED
                    ? 'Cancelado por el comercio. Motivo: '.$request->cancellation_reason
                    : 'Actualizado por el comercio a: '.ucfirst($newStatus),
                'changed_by_user_id' => auth()->id(),
            ]);
        });

        return back()->with('success', 'El estado del pedido fue actualizado exitosamente.');
    }

    /**
     * Assign a driver and dispatch the order (transitions status to on_the_way).
     */
    public function assignDriver(Order $order, Request $request): RedirectResponse
    {
        if (! $this->checkSellerAccess($order)) {
            abort(403, 'No tienes autorización para gestionar este pedido.');
        }

        $request->validate([
            'driver_profile_id' => ['required', 'uuid', 'exists:driver_profiles,id'],
        ]);

        $firstItem = $order->items()->first();
        if (! $firstItem) {
            return back()->withErrors(['driver_profile_id' => 'No se puede despachar un pedido sin productos.']);
        }

        $driver = DriverProfile::findOrFail($request->driver_profile_id);

        DB::transaction(function () use ($order, $driver, $firstItem) {
            // 1. Create or update delivery record with status assigned
            $delivery = Delivery::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'driver_profile_id' => $driver->id,
                    'business_id' => $firstItem->business_id,
                    'status' => Delivery::STATUS_ASSIGNED,
                    'assigned_at' => now(),
                    'picked_up_at' => null,
                    'delivered_at' => null,
                ]
            );

            // 2. Create or update driver assignment
            DriverAssignment::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'driver_profile_id' => $driver->id,
                ],
                [
                    'delivery_id' => $delivery->id,
                    'status' => DriverAssignment::STATUS_ASSIGNED,
                    'assigned_at' => now(),
                    'accepted_at' => null,
                    'completed_at' => null,
                ]
            );

            // 3. Log status change history (Repartidor asignado)
            $driverName = $driver->user->first_name.' '.$driver->user->last_name;
            $order->statusHistory()->create([
                'status' => $order->status,
                'description' => "Repartidor asignado: {$driverName}. Esperando aceptación.",
                'changed_by_user_id' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Pedido asignado al repartidor. Esperando aceptación.');
    }

    /**
     * Check if the authenticated user has access to view/manage this order as a seller.
     */
    private function checkSellerAccess(Order $order): bool
    {
        $user = auth()->user();

        // Super Admin is always allowed
        if ($user->hasRole(Role::SUPER_ADMIN)) {
            return true;
        }

        // Must have seller role
        if (! $user->hasRole(Role::SELLER)) {
            return false;
        }

        // Seller must be associated with the business of the order
        $firstItem = $order->items()->first();
        if ($firstItem && $firstItem->business_id) {
            return DB::table('business_users')
                ->where('business_id', $firstItem->business_id)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->exists();
        }

        return false;
    }
}
