<?php

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\Delivery;
use App\Models\DriverAssignment;
use App\Models\DriverProfile;
use App\Models\Order;
use App\Models\OrderItem;
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
    ]);

    // Create a business and location
    $this->business = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);

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
        'order_number' => 'NKM-API-DRV-TEST',
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

test('guest users cannot access driver dashboard or assignments endpoints', function () {
    $this->getJson('/api/v1/driver/dashboard')->assertStatus(401);
    $this->postJson("/api/v1/driver/assignments/{$this->assignment->id}/accept")->assertStatus(401);
});

test('non-drivers cannot access driver endpoints', function () {
    $this->actingAs($this->customer, 'sanctum')->getJson('/api/v1/driver/dashboard')->assertForbidden();
});

test('drivers can access dashboard API', function () {
    $response = $this->actingAs($this->driverUser, 'sanctum')
        ->getJson('/api/v1/driver/dashboard');

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
    ]);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'today_completed_count',
            'today_failed_count',
            'active_delivery',
            'pending_assignments',
        ],
    ]);
});

test('drivers can accept a pending assignment via API', function () {
    $response = $this->actingAs($this->driverUser, 'sanctum')
        ->postJson("/api/v1/driver/assignments/{$this->assignment->id}/accept");

    $response->assertSuccessful();
    $response->assertJson(['success' => true, 'message' => 'Pedido aceptado correctamente.']);

    $this->assertEquals(DriverAssignment::STATUS_ACCEPTED, $this->assignment->fresh()->status);
    $this->assertEquals(Delivery::STATUS_ASSIGNED, $this->delivery->fresh()->status);
});

test('other drivers cannot accept another driver assignment', function () {
    $this->actingAs($this->otherDriverUser, 'sanctum')
        ->postJson("/api/v1/driver/assignments/{$this->assignment->id}/accept")
        ->assertNotFound();
});

test('drivers can reject a pending assignment via API', function () {
    $response = $this->actingAs($this->driverUser, 'sanctum')
        ->postJson("/api/v1/driver/assignments/{$this->assignment->id}/reject");

    $response->assertSuccessful();
    $response->assertJson(['success' => true, 'message' => 'Asignación rechazada correctamente.']);

    $this->assertEquals(DriverAssignment::STATUS_REJECTED, $this->assignment->fresh()->status);
    $this->assertNull($this->delivery->fresh()->driver_profile_id);
    $this->assertEquals(Delivery::STATUS_PENDING, $this->delivery->fresh()->status);
});

test('drivers can emit live coordinates via API', function () {
    $response = $this->actingAs($this->driverUser, 'sanctum')
        ->postJson("/api/v1/driver/deliveries/{$this->delivery->id}/emit-location", [
            'latitude' => -6.7722,
            'longitude' => -79.8455,
        ]);

    $response->assertSuccessful();
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('driver_live_locations', [
        'driver_profile_id' => $this->driverProfile->id,
        'latitude' => -6.7722,
        'longitude' => -79.8455,
    ]);

    $this->assertEquals(Order::STATUS_ON_THE_WAY, $this->order->fresh()->status);
});

test('drivers can complete delivery via API', function () {
    $response = $this->actingAs($this->driverUser, 'sanctum')
        ->postJson("/api/v1/driver/deliveries/{$this->delivery->id}/complete");

    $response->assertSuccessful();

    $this->assertEquals(Order::STATUS_DELIVERED, $this->order->fresh()->status);
    $this->assertEquals(Delivery::STATUS_DELIVERED, $this->delivery->fresh()->status);
    $this->assertEquals(DriverAssignment::STATUS_COMPLETED, $this->assignment->fresh()->status);
    $this->assertEquals(1, $this->driverProfile->fresh()->total_deliveries);
});

test('drivers can reject delivery via API', function () {
    Payment::create([
        'order_id' => $this->order->id,
        'payment_method' => Payment::METHOD_CARD,
        'amount' => $this->order->total,
        'status' => Payment::STATUS_PAID,
        'transaction_id' => 'TXN-API-DRV',
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($this->driverUser, 'sanctum')
        ->postJson("/api/v1/driver/deliveries/{$this->delivery->id}/client-reject", [
            'rejection_reason' => 'Cliente canceló el pedido al llegar.',
        ]);

    $response->assertSuccessful();

    $this->assertEquals(Order::STATUS_CANCELLED, $this->order->fresh()->status);
    $this->assertEquals(Delivery::STATUS_FAILED, $this->delivery->fresh()->status);
    $this->assertEquals(DriverAssignment::STATUS_COMPLETED, $this->assignment->fresh()->status);
});
