<?php

use App\Models\Business;
use App\Models\BusinessUser;
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

    // Create roles
    $this->sellerRole = Role::where('slug', Role::SELLER)->first();
    $this->customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $this->driverRole = Role::where('slug', Role::DRIVER)->first();

    // Create Seller 1 (Business 1)
    $this->seller1 = User::factory()->create();
    $this->seller1->roles()->attach($this->sellerRole->id);

    $this->business1 = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);

    BusinessUser::create([
        'business_id' => $this->business1->id,
        'user_id' => $this->seller1->id,
        'role' => BusinessUser::ROLE_ADMIN,
        'is_active' => true,
        'joined_at' => now(),
    ]);

    // Create Seller 2 (Business 2)
    $this->seller2 = User::factory()->create();
    $this->seller2->roles()->attach($this->sellerRole->id);

    $this->business2 = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);

    BusinessUser::create([
        'business_id' => $this->business2->id,
        'user_id' => $this->seller2->id,
        'role' => BusinessUser::ROLE_ADMIN,
        'is_active' => true,
        'joined_at' => now(),
    ]);

    // Create generic customer
    $this->customer = User::factory()->create();
    $this->customer->roles()->attach($this->customerRole->id);

    // Create active driver
    $this->driverUser = User::factory()->create();
    $this->driverUser->roles()->attach($this->driverRole->id);

    $this->driverProfile = DriverProfile::factory()->create([
        'user_id' => $this->driverUser->id,
        'status' => DriverProfile::STATUS_ACTIVE,
    ]);

    // Create order from Customer for Business 1
    $this->order1 = Order::create([
        'user_id' => $this->customer->id,
        'order_number' => 'NKM-B1-ORDER',
        'status' => Order::STATUS_PENDING,
        'subtotal' => 30.00,
        'delivery_fee' => 5.00,
        'total' => 35.00,
        'delivery_address' => 'Miraflores, Lima',
        'delivery_latitude' => -12.1221,
        'delivery_longitude' => -77.0298,
    ]);

    OrderItem::create([
        'order_id' => $this->order1->id,
        'business_id' => $this->business1->id,
        'product_name' => 'Burger',
        'unit_price' => 30.00,
        'quantity' => 1,
        'subtotal' => 30.00,
    ]);

    // Create order from Customer for Business 2
    $this->order2 = Order::create([
        'user_id' => $this->customer->id,
        'order_number' => 'NKM-B2-ORDER',
        'status' => Order::STATUS_PENDING,
        'subtotal' => 45.00,
        'delivery_fee' => 5.00,
        'total' => 50.00,
        'delivery_address' => 'Miraflores, Lima',
        'delivery_latitude' => -12.1221,
        'delivery_longitude' => -77.0298,
    ]);

    OrderItem::create([
        'order_id' => $this->order2->id,
        'business_id' => $this->business2->id,
        'product_name' => 'Pizza',
        'unit_price' => 45.00,
        'quantity' => 1,
        'subtotal' => 45.00,
    ]);
});

test('guests are redirected to login when trying to view seller orders', function () {
    $this->get(route('seller.orders.index'))
        ->assertRedirect(route('login'));

    $this->get(route('seller.orders.show', $this->order1))
        ->assertRedirect(route('login'));

    $this->post(route('seller.orders.updateStatus', $this->order1), ['status' => 'confirmed'])
        ->assertRedirect(route('login'));

    $this->post(route('seller.orders.assignDriver', $this->order1), ['driver_profile_id' => $this->driverProfile->id])
        ->assertRedirect(route('login'));
});

test('non-sellers cannot access seller orders', function () {
    $this->actingAs($this->customer)
        ->get(route('seller.orders.index'))
        ->assertForbidden();

    $this->actingAs($this->customer)
        ->get(route('seller.orders.show', $this->order1))
        ->assertForbidden();
});

test('sellers can only view orders belonging to their business', function () {
    // 1. Seller 1 accesses index and sees NKM-B1-ORDER, but not NKM-B2-ORDER
    $response = $this->actingAs($this->seller1)
        ->get(route('seller.orders.index'));

    $response->assertSuccessful()
        ->assertSee('NKM-B1-ORDER')
        ->assertDontSee('NKM-B2-ORDER');

    // 2. Seller 1 views details of business 1 order
    $this->actingAs($this->seller1)
        ->get(route('seller.orders.show', $this->order1))
        ->assertSuccessful()
        ->assertSee('NKM-B1-ORDER')
        ->assertSee('Burger');

    // 3. Seller 1 cannot view business 2 order details (403 Forbidden)
    $this->actingAs($this->seller1)
        ->get(route('seller.orders.show', $this->order2))
        ->assertForbidden();
});

test('sellers can update status of their order', function () {
    // Seller 1 accepts order 1
    $response = $this->actingAs($this->seller1)
        ->post(route('seller.orders.updateStatus', $this->order1), [
            'status' => 'confirmed',
        ]);

    $response->assertRedirect();
    $this->assertEquals(Order::STATUS_CONFIRMED, $this->order1->fresh()->status);
    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $this->order1->id,
        'status' => 'confirmed',
    ]);
});

test('sellers can assign driver and dispatch order', function () {
    // Seller 1 assigns order 1 to driver
    $response = $this->actingAs($this->seller1)
        ->post(route('seller.orders.assignDriver', $this->order1), [
            'driver_profile_id' => $this->driverProfile->id,
        ]);

    $response->assertRedirect();

    // Order status should remain pending
    $this->assertEquals(Order::STATUS_PENDING, $this->order1->fresh()->status);

    // Delivery record should be created with status assigned
    $this->assertDatabaseHas('deliveries', [
        'order_id' => $this->order1->id,
        'driver_profile_id' => $this->driverProfile->id,
        'status' => Delivery::STATUS_ASSIGNED,
    ]);

    // Check DriverAssignment is created
    $assignment = DriverAssignment::where('order_id', $this->order1->id)
        ->where('driver_profile_id', $this->driverProfile->id)
        ->first();
    $this->assertNotNull($assignment);
    $this->assertEquals(DriverAssignment::STATUS_ASSIGNED, $assignment->status);

    // Transition should be recorded as assigned
    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $this->order1->id,
        'status' => Order::STATUS_PENDING,
    ]);

    // Driver accepts the assignment
    $this->actingAs($this->driverUser)
        ->post(route('driver.assignments.accept', $assignment))
        ->assertRedirect();

    $this->assertEquals(DriverAssignment::STATUS_ACCEPTED, $assignment->fresh()->status);

    // Driver starts route / emits location, transitioning status to on_the_way
    $delivery = $this->order1->deliveries()->first();
    $this->actingAs($this->driverUser)
        ->postJson(route('driver.deliveries.emitLocation', $delivery), [
            'latitude' => -12.1221,
            'longitude' => -77.0298,
        ])
        ->assertSuccessful();

    $this->assertEquals(Order::STATUS_ON_THE_WAY, $this->order1->fresh()->status);
    $this->assertEquals(Delivery::STATUS_ON_THE_WAY, $delivery->fresh()->status);

    // Transition should be recorded
    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $this->order1->id,
        'status' => Order::STATUS_ON_THE_WAY,
    ]);
});

test('sellers cannot cancel order without reason', function () {
    $this->actingAs($this->seller1)
        ->post(route('seller.orders.updateStatus', $this->order1), [
            'status' => 'cancelled',
        ])
        ->assertSessionHasErrors(['cancellation_reason']);
});

test('sellers can cancel order with reason and it triggers digital refund if paid', function () {
    // 1. Create a payment record and mark it paid for order1
    $payment = Payment::create([
        'order_id' => $this->order1->id,
        'payment_method' => Payment::METHOD_CARD,
        'amount' => $this->order1->total,
        'status' => Payment::STATUS_PAID,
        'transaction_id' => 'TXN-MOCK123',
        'paid_at' => now(),
    ]);

    $this->order1->update([
        'payment_status' => Order::PAYMENT_STATUS_PAID,
    ]);

    // 2. Perform cancellation
    $response = $this->actingAs($this->seller1)
        ->post(route('seller.orders.updateStatus', $this->order1), [
            'status' => 'cancelled',
            'cancellation_reason' => 'Establecimiento cerrado temporalmente por avería.',
        ]);

    $response->assertRedirect();

    // 3. Assert order status is cancelled and payment_status is refunded
    $this->assertEquals(Order::STATUS_CANCELLED, $this->order1->fresh()->status);
    $this->assertEquals(Order::PAYMENT_STATUS_REFUNDED, $this->order1->fresh()->payment_status);

    // 4. Assert cancellation is registered
    $this->assertDatabaseHas('order_cancellations', [
        'order_id' => $this->order1->id,
        'cancelled_by_type' => OrderCancellation::BY_BUSINESS,
        'comment' => 'Establecimiento cerrado temporalmente por avería.',
    ]);

    // 5. Assert refund is registered
    $this->assertDatabaseHas('order_refunds', [
        'order_id' => $this->order1->id,
        'payment_id' => $payment->id,
        'amount' => $this->order1->total,
        'status' => OrderRefund::STATUS_PROCESSED,
    ]);

    // 6. Assert payment status updated to refunded
    $this->assertEquals(Payment::STATUS_REFUNDED, $payment->fresh()->status);
});
