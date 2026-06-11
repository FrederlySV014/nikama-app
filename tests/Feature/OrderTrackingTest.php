<?php

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\Delivery;
use App\Models\DriverProfile;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Create a customer user
    $this->customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $this->customer->roles()->attach($customerRole->id);

    // Create another customer user for security tests
    $this->otherCustomer = User::factory()->create();
    $this->otherCustomer->roles()->attach($customerRole->id);

    // Create a merchant/business
    $this->business = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);

    // Create business location in Chiclayo
    $this->businessLocation = BusinessLocation::factory()->create([
        'business_id' => $this->business->id,
        'latitude' => -6.7719000,
        'longitude' => -79.8441000,
        'is_main' => true,
        'is_active' => true,
    ]);

    // Create a seller user
    $this->seller = User::factory()->create();
    $sellerRole = Role::where('slug', Role::SELLER)->first();
    $this->seller->roles()->attach($sellerRole->id);

    // Associate seller user to business in business_users
    DB::table('business_users')->insert([
        'id' => (string) Str::uuid(),
        'business_id' => $this->business->id,
        'user_id' => $this->seller->id,
        'role' => 'admin',
        'is_active' => true,
        'joined_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create a super admin user
    $this->admin = User::factory()->create();
    $adminRole = Role::where('slug', Role::SUPER_ADMIN)->first();
    $this->admin->roles()->attach($adminRole->id);

    // Create order for customer
    $this->order = Order::create([
        'user_id' => $this->customer->id,
        'order_number' => 'NKM-TRACK123',
        'status' => Order::STATUS_CONFIRMED,
        'subtotal' => 15.00,
        'delivery_fee' => 5.00,
        'total' => 20.00,
        'delivery_address' => 'Urbanización Hipólito Unanue, Chiclayo',
        'delivery_latitude' => -6.7725000,
        'delivery_longitude' => -79.8465000,
    ]);

    // Create order item linking to the business
    $this->orderItem = OrderItem::create([
        'order_id' => $this->order->id,
        'business_id' => $this->business->id,
        'product_name' => 'Pizza Familiar',
        'unit_price' => 15.00,
        'quantity' => 1,
        'subtotal' => 15.00,
    ]);
});

test('guests are redirected to login when trying to view orders', function () {
    $this->get(route('orders.index'))
        ->assertRedirect(route('login'));

    $this->get(route('orders.show', $this->order))
        ->assertRedirect(route('login'));

    $this->get(route('orders.location', $this->order))
        ->assertRedirect(route('login'));

    $this->post(route('orders.simulateStatus', $this->order), ['status' => 'preparing'])
        ->assertRedirect(route('login'));
});

test('customers can view their own orders list', function () {
    $response = $this->actingAs($this->customer)
        ->get(route('orders.index'));

    $response->assertSuccessful()
        ->assertViewIs('public.orders.index')
        ->assertSee('NKM-TRACK123')
        ->assertSee('S/ 20.00');
});

test('customers cannot view another customer\'s orders', function () {
    // Attempt to view index of another customer is OK (they see their own empty list)
    $responseIndex = $this->actingAs($this->otherCustomer)
        ->get(route('orders.index'));

    $responseIndex->assertSuccessful()
        ->assertDontSee('NKM-TRACK123');

    // Attempt to view details of another customer's order results in 403 Forbidden
    $this->actingAs($this->otherCustomer)
        ->get(route('orders.show', $this->order))
        ->assertForbidden();

    // Attempt to view location coordinates of another customer's order results in 403 Forbidden
    $this->actingAs($this->otherCustomer)
        ->get(route('orders.location', $this->order))
        ->assertForbidden();

    // Attempt to simulate status change of another customer's order results in 403 Forbidden
    $this->actingAs($this->otherCustomer)
        ->post(route('orders.simulateStatus', $this->order), ['status' => 'preparing'])
        ->assertForbidden();
});

test('location endpoint returns coordinates of business, customer and driver', function () {
    $response = $this->actingAs($this->customer)
        ->getJson(route('orders.location', $this->order));

    $response->assertSuccessful()
        ->assertJsonStructure([
            'status',
            'origin' => ['latitude', 'longitude', 'name'],
            'destination' => ['latitude', 'longitude', 'address'],
            'driver' => ['latitude', 'longitude'],
            'simulation' => ['percentage', 'elapsed_seconds', 'total_seconds'],
        ]);

    // Since status is confirmed, driver should be at business location
    $data = $response->json();
    $this->assertEquals(-6.7719, $data['driver']['latitude']);
    $this->assertEquals(-79.8441, $data['driver']['longitude']);
});

test('customers cannot simulate status transitions on their orders', function () {
    $this->actingAs($this->customer)
        ->postJson(route('orders.simulateStatus', $this->order), ['status' => 'preparing'])
        ->assertForbidden();
});

test('sellers associated with the business and admins can simulate status transitions', function () {
    // 1. Transition to preparing via seller
    $response = $this->actingAs($this->seller)
        ->postJson(route('orders.simulateStatus', $this->order), ['status' => 'preparing']);

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'status' => 'preparing',
        ]);

    $this->assertDatabaseHas('orders', [
        'id' => $this->order->id,
        'status' => 'preparing',
    ]);

    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $this->order->id,
        'status' => 'preparing',
    ]);

    // 2. Transition to on_the_way via super admin
    $responseOnWay = $this->actingAs($this->admin)
        ->postJson(route('orders.simulateStatus', $this->order), ['status' => 'on_the_way']);

    $responseOnWay->assertSuccessful()
        ->assertJson([
            'status' => 'on_the_way',
        ]);

    // 3. Verify location math interpolation immediately after dispatching (elapsed seconds near 0)
    $responseLoc = $this->actingAs($this->customer)
        ->getJson(route('orders.location', $this->order));

    $locData = $responseLoc->json();
    // percentage should be near 0 (e.g. 0.0)
    $this->assertEquals(0, $locData['simulation']['percentage']);
    $this->assertEquals(-6.7719, $locData['driver']['latitude']);
    $this->assertEquals(-79.8441, $locData['driver']['longitude']);

    // 4. Transition to delivered via seller
    $responseDelivered = $this->actingAs($this->seller)
        ->postJson(route('orders.simulateStatus', $this->order), ['status' => 'delivered']);

    $responseDelivered->assertSuccessful();

    // Verify driver is at customer location now
    $responseLocDelivered = $this->actingAs($this->customer)
        ->getJson(route('orders.location', $this->order));

    $locDataDelivered = $responseLocDelivered->json();
    $this->assertEquals(1.0, $locDataDelivered['simulation']['percentage']);
    $this->assertEquals(-6.7725, $locDataDelivered['driver']['latitude']);
    $this->assertEquals(-79.8465, $locDataDelivered['driver']['longitude']);

    $this->assertDatabaseHas('orders', [
        'id' => $this->order->id,
        'status' => 'delivered',
        'payment_status' => 'paid',
    ]);
});

test('cannot simulate an invalid status transition', function () {
    $this->actingAs($this->admin)
        ->postJson(route('orders.simulateStatus', $this->order), ['status' => 'invalid_status_name'])
        ->assertStatus(422);
});

test('customers cannot rate driver if order is not delivered', function () {
    $this->actingAs($this->customer)
        ->post(route('orders.rate-driver', $this->order), [
            'rating' => 5,
            'comment' => 'Excelente servicio',
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});

test('customers can rate driver when order is delivered, which updates driver rating average', function () {
    // 1. Set status to delivered
    $this->order->update([
        'status' => Order::STATUS_DELIVERED,
    ]);

    // 2. Create driver profile and delivery record
    $driverUser = User::factory()->create();
    $driverRole = Role::where('slug', Role::DRIVER)->first();
    $driverUser->roles()->attach($driverRole->id);
    $driverProfile = DriverProfile::factory()->create([
        'user_id' => $driverUser->id,
        'status' => DriverProfile::STATUS_ACTIVE,
        'rating_average' => 0.00,
    ]);

    $delivery = Delivery::create([
        'order_id' => $this->order->id,
        'driver_profile_id' => $driverProfile->id,
        'business_id' => $this->business->id,
        'status' => Delivery::STATUS_DELIVERED,
        'assigned_at' => now(),
        'delivered_at' => now(),
    ]);

    // 3. Post rating
    $response = $this->actingAs($this->customer)
        ->post(route('orders.rate-driver', $this->order), [
            'rating' => 4,
            'comment' => 'El repartidor fue muy amable.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // 4. Verify review is created
    $this->assertDatabaseHas('driver_reviews', [
        'order_id' => $this->order->id,
        'driver_profile_id' => $driverProfile->id,
        'user_id' => $this->customer->id,
        'rating' => 4,
        'comment' => 'El repartidor fue muy amable.',
    ]);

    // 5. Verify driver average rating is updated
    $this->assertEquals(4.00, $driverProfile->fresh()->rating_average);

    // 6. Verify duplicate rating is rejected
    $this->actingAs($this->customer)
        ->post(route('orders.rate-driver', $this->order), [
            'rating' => 5,
            'comment' => 'Otra calificación',
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});
