<?php

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\Delivery;
use App\Models\DriverAssignment;
use App\Models\DriverProfile;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderItem;
use App\Models\OrderRefund;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $this->driverRole = Role::where('slug', Role::DRIVER)->first();
    $this->sellerRole = Role::where('slug', Role::SELLER)->first();

    // Create a customer
    $this->customer = User::factory()->create();
    $this->customer->roles()->attach($this->customerRole->id);

    // Create a driver user
    $this->driverUser = User::factory()->create();
    $this->driverUser->roles()->attach($this->driverRole->id);
    $this->driverProfile = DriverProfile::factory()->create([
        'user_id' => $this->driverUser->id,
        'status' => DriverProfile::STATUS_ACTIVE,
        'total_deliveries' => 0,
    ]);

    // Create another driver user
    $this->otherDriverUser = User::factory()->create();
    $this->otherDriverUser->roles()->attach($this->driverRole->id);
    $this->otherDriverProfile = DriverProfile::factory()->create([
        'user_id' => $this->otherDriverUser->id,
        'status' => DriverProfile::STATUS_ACTIVE,
        'total_deliveries' => 0,
    ]);

    // Create a business
    $this->business = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);

    // Create business location
    $this->businessLocation = BusinessLocation::factory()->create([
        'business_id' => $this->business->id,
        'latitude' => -6.7719,
        'longitude' => -79.8441,
        'is_main' => true,
        'is_active' => true,
    ]);

    // Create order
    $this->order = Order::create([
        'user_id' => $this->customer->id,
        'order_number' => 'NKM-DRV-TEST',
        'status' => Order::STATUS_CONFIRMED,
        'subtotal' => 20.00,
        'delivery_fee' => 5.00,
        'total' => 25.00,
        'delivery_address' => 'Urbanización Hipólito Unanue, Chiclayo',
        'delivery_latitude' => -6.7725,
        'delivery_longitude' => -79.8465,
    ]);

    $this->orderItem = OrderItem::create([
        'order_id' => $this->order->id,
        'business_id' => $this->business->id,
        'product_name' => 'Comida de Prueba',
        'unit_price' => 20.00,
        'quantity' => 1,
        'subtotal' => 20.00,
    ]);

    // Create delivery in assigned status
    $this->delivery = Delivery::create([
        'order_id' => $this->order->id,
        'driver_profile_id' => $this->driverProfile->id,
        'business_id' => $this->business->id,
        'status' => Delivery::STATUS_ASSIGNED,
        'assigned_at' => now(),
    ]);

    // Create assignment
    $this->assignment = DriverAssignment::create([
        'order_id' => $this->order->id,
        'driver_profile_id' => $this->driverProfile->id,
        'delivery_id' => $this->delivery->id,
        'status' => DriverAssignment::STATUS_ASSIGNED,
        'assigned_at' => now(),
    ]);
});

test('guest users are redirected to login', function () {
    $this->get(route('driver.dashboard'))->assertRedirect(route('login'));
    $this->post(route('driver.assignments.accept', $this->assignment))->assertRedirect(route('login'));
    $this->post(route('driver.assignments.reject', $this->assignment))->assertRedirect(route('login'));
    $this->get(route('driver.deliveries.show', $this->delivery))->assertRedirect(route('login'));
    $this->post(route('driver.deliveries.emitLocation', $this->delivery))->assertRedirect(route('login'));
    $this->post(route('driver.deliveries.complete', $this->delivery))->assertRedirect(route('login'));
});

test('non-drivers cannot access driver routes', function () {
    $this->actingAs($this->customer);

    $this->get(route('driver.dashboard'))->assertForbidden();
    $this->post(route('driver.assignments.accept', $this->assignment))->assertForbidden();
    $this->post(route('driver.assignments.reject', $this->assignment))->assertForbidden();
    $this->get(route('driver.deliveries.show', $this->delivery))->assertForbidden();
    $this->post(route('driver.deliveries.emitLocation', $this->delivery))->assertForbidden();
    $this->post(route('driver.deliveries.complete', $this->delivery))->assertForbidden();
});

test('drivers can access their dashboard', function () {
    $response = $this->actingAs($this->driverUser)
        ->get(route('driver.dashboard'));

    $response->assertSuccessful()
        ->assertViewIs('driver.dashboard')
        ->assertSee('NKM-DRV-TEST');
});

test('drivers can accept a pending assignment', function () {
    $response = $this->actingAs($this->driverUser)
        ->post(route('driver.assignments.accept', $this->assignment));

    $response->assertRedirect();

    $this->assertEquals(DriverAssignment::STATUS_ACCEPTED, $this->assignment->fresh()->status);
    $this->assertEquals(Delivery::STATUS_ASSIGNED, $this->delivery->fresh()->status);
    $this->assertEquals(Order::STATUS_CONFIRMED, $this->order->fresh()->status); // Order status is not on_the_way yet!

    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $this->order->id,
        'status' => Order::STATUS_CONFIRMED,
    ]);
});

test('other drivers cannot accept an assignment that is not theirs', function () {
    $this->actingAs($this->otherDriverUser)
        ->post(route('driver.assignments.accept', $this->assignment))
        ->assertForbidden();
});

test('drivers can reject a pending assignment', function () {
    $response = $this->actingAs($this->driverUser)
        ->post(route('driver.assignments.reject', $this->assignment));

    $response->assertRedirect();

    $this->assertEquals(DriverAssignment::STATUS_REJECTED, $this->assignment->fresh()->status);
    $this->assertNull($this->delivery->fresh()->driver_profile_id);
    $this->assertEquals(Delivery::STATUS_PENDING, $this->delivery->fresh()->status);
});

test('drivers can view their active delivery page', function () {
    $response = $this->actingAs($this->driverUser)
        ->get(route('driver.deliveries.show', $this->delivery));

    $response->assertSuccessful()
        ->assertViewIs('driver.deliveries.show')
        ->assertSee($this->order->order_number);
});

test('other drivers cannot view a delivery page not assigned to them', function () {
    $this->actingAs($this->otherDriverUser)
        ->get(route('driver.deliveries.show', $this->delivery))
        ->assertForbidden();
});

test('drivers can emit live location coordinates, which updates order status to on_the_way', function () {
    $response = $this->actingAs($this->driverUser)
        ->postJson(route('driver.deliveries.emitLocation', $this->delivery), [
            'latitude' => -6.7721,
            'longitude' => -79.8450,
        ]);

    $response->assertSuccessful()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('driver_live_locations', [
        'driver_profile_id' => $this->driverProfile->id,
        'latitude' => -6.7721,
        'longitude' => -79.8450,
    ]);

    $this->assertEquals(Order::STATUS_ON_THE_WAY, $this->order->fresh()->status);
    $this->assertEquals(Delivery::STATUS_ON_THE_WAY, $this->delivery->fresh()->status);

    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $this->order->id,
        'status' => Order::STATUS_ON_THE_WAY,
    ]);
});

test('drivers can complete an active delivery', function () {
    // First, change status to on_the_way through emitLocation to simulate route started
    $this->actingAs($this->driverUser)
        ->postJson(route('driver.deliveries.emitLocation', $this->delivery), [
            'latitude' => -6.7721,
            'longitude' => -79.8450,
        ]);

    $response = $this->actingAs($this->driverUser)
        ->post(route('driver.deliveries.complete', $this->delivery));

    $response->assertRedirect(route('driver.dashboard'));

    $this->assertEquals(Order::STATUS_DELIVERED, $this->order->fresh()->status);
    $this->assertEquals(Delivery::STATUS_DELIVERED, $this->delivery->fresh()->status);
    $this->assertEquals(DriverAssignment::STATUS_COMPLETED, $this->assignment->fresh()->status);
    $this->assertEquals(1, $this->driverProfile->fresh()->total_deliveries);
});

test('drivers can report delivery rejected by client', function () {
    // 1. Setup payment record
    $payment = Payment::create([
        'order_id' => $this->order->id,
        'payment_method' => Payment::METHOD_CARD,
        'amount' => $this->order->total,
        'status' => Payment::STATUS_PAID,
        'transaction_id' => 'TXN-DRV123',
        'paid_at' => now(),
    ]);

    $this->order->update([
        'payment_status' => Order::PAYMENT_STATUS_PAID,
    ]);

    // 2. Perform client reject
    $response = $this->actingAs($this->driverUser)
        ->post(route('driver.deliveries.client-reject', $this->delivery), [
            'rejection_reason' => 'Cliente indica que tardó demasiado y ya no lo quiere.',
        ]);

    $response->assertRedirect(route('driver.dashboard'));

    // 3. Assert status updates
    $this->assertEquals(Order::STATUS_CANCELLED, $this->order->fresh()->status);
    $this->assertEquals(Order::PAYMENT_STATUS_REFUNDED, $this->order->fresh()->payment_status);
    $this->assertEquals(Delivery::STATUS_FAILED, $this->delivery->fresh()->status);
    $this->assertEquals(DriverAssignment::STATUS_COMPLETED, $this->assignment->fresh()->status);

    // 4. Assert cancellation comment
    $this->assertDatabaseHas('order_cancellations', [
        'order_id' => $this->order->id,
        'cancelled_by_type' => OrderCancellation::BY_CUSTOMER,
        'comment' => 'Cliente indica que tardó demasiado y ya no lo quiere.',
    ]);

    // 5. Assert refund is registered
    $this->assertDatabaseHas('order_refunds', [
        'order_id' => $this->order->id,
        'payment_id' => $payment->id,
        'amount' => $this->order->total,
        'status' => OrderRefund::STATUS_PROCESSED,
    ]);

    // 6. Assert driver availability is updated back to available
    $this->assertDatabaseHas('driver_live_locations', [
        'driver_profile_id' => $this->driverProfile->id,
        'is_available' => true,
    ]);
});

test('drivers can view their delivery history', function () {
    $response = $this->actingAs($this->driverUser)
        ->get(route('driver.history'));

    $response->assertSuccessful()
        ->assertViewIs('driver.history')
        ->assertSee('NKM-DRV-TEST');
});
