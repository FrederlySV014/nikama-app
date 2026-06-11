<?php

use App\Models\Business;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Create a client user
    $this->customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $this->customer->roles()->attach($customerRole->id);

    // Create a super admin user
    $this->admin = User::factory()->create();
    $adminRole = Role::where('slug', Role::SUPER_ADMIN)->first();
    $this->admin->roles()->attach($adminRole->id);

    // Create default merchant/business
    $this->business = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);

    // Create a product
    $this->product = Product::factory()->create([
        'business_id' => $this->business->id,
        'price' => 15.00,
        'status' => Product::STATUS_ACTIVE,
    ]);

    // Create customer address
    $this->address = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'department' => 'Lima',
        'province' => 'Lima',
        'district' => 'Miraflores',
        'is_default' => true,
    ]);

    // Seed active districts for test compatibility
    SystemSetting::updateOrCreate(
        ['key' => 'active_districts'],
        ['value' => json_encode([
            'Lambayeque|Chiclayo|Chiclayo',
            'Lambayeque|Chiclayo|José Leonardo Ortiz',
            'Lambayeque|Chiclayo|La Victoria',
            'Lambayeque|Chiclayo|Pimentel',
            'Lima|Lima|Miraflores',
            'Lima|Lima|San Isidro',
            'Lambayeque|Chiclayo|Trabajo Temporal',
            'Lambayeque|Chiclayo|Casa de Verano',
        ])]
    );
});

test('guests are redirected to login when accessing checkout', function () {
    $this->get(route('checkout.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users with empty cart are redirected to welcome page', function () {
    $this->actingAs($this->customer)
        ->get(route('checkout.index'))
        ->assertRedirect(route('public.welcome'))
        ->assertSessionHas('error');
});

test('authenticated users with items in cart can view checkout index', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 2,
        'unit_price' => 15.00,
    ]);

    $response = $this->actingAs($this->customer)
        ->get(route('checkout.index'));

    $response->assertStatus(200)
        ->assertViewIs('public.checkout.index')
        ->assertViewHasAll(['cart', 'addresses', 'activeMethods', 'subtotal', 'deliveryFee', 'total']);
});

test('authenticated users can place cash orders and order goes directly to confirmed', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $response = $this->actingAs($this->customer)
        ->post(route('checkout.store'), [
            'address_selection_type' => 'saved',
            'customer_address_id' => $this->address->id,
            'payment_method' => Payment::METHOD_CASH,
            'notes' => 'Tocar timbre fuerte',
        ]);

    $order = Order::latest()->first();

    $response->assertRedirect(route('checkout.success', $order));

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'pending',
        'payment_status' => 'pending',
        'delivery_address' => $this->address->address,
    ]);

    $this->assertDatabaseHas('payments', [
        'order_id' => $order->id,
        'payment_method' => Payment::METHOD_CASH,
        'status' => Payment::STATUS_PENDING,
    ]);

    $this->assertEquals(Cart::STATUS_CONVERTED, $cart->fresh()->status);
});

test('authenticated users placing yape/plin/card orders get auto-confirmed and redirected to success', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $response = $this->actingAs($this->customer)
        ->post(route('checkout.store'), [
            'address_selection_type' => 'saved',
            'customer_address_id' => $this->address->id,
            'payment_method' => Payment::METHOD_YAPE,
        ]);

    $order = Order::latest()->first();
    $payment = Payment::latest()->first();

    $response->assertRedirect(route('checkout.success', $order));

    $this->assertEquals('pending', $order->fresh()->status);
    $this->assertEquals('paid', $order->fresh()->payment_status);
    $this->assertEquals(Payment::STATUS_PAID, $payment->fresh()->status);
});

test('digital payment auto-confirm creates a status history entry', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $this->actingAs($this->customer)
        ->post(route('checkout.store'), [
            'address_selection_type' => 'saved',
            'customer_address_id' => $this->address->id,
            'payment_method' => Payment::METHOD_PLIN,
        ]);

    $order = Order::latest()->first();

    $this->assertDatabaseHas('order_status_history', [
        'order_id' => $order->id,
        'status' => 'pending',
    ]);
});

test('card payment auto-confirms order and sets payment to paid', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $this->actingAs($this->customer)
        ->post(route('checkout.store'), [
            'address_selection_type' => 'saved',
            'customer_address_id' => $this->address->id,
            'payment_method' => Payment::METHOD_CARD,
        ]);

    $order = Order::latest()->first();
    $payment = Payment::latest()->first();

    $this->assertEquals('pending', $order->fresh()->status);
    $this->assertEquals('paid', $order->fresh()->payment_status);
    $this->assertEquals(Payment::STATUS_PAID, $payment->fresh()->status);
    $this->assertNotNull($payment->fresh()->transaction_id);
    $this->assertNotNull($payment->fresh()->paid_at);
});

test('cash payment confirms order with pending payment status', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $this->actingAs($this->customer)
        ->post(route('checkout.store'), [
            'address_selection_type' => 'saved',
            'customer_address_id' => $this->address->id,
            'payment_method' => Payment::METHOD_CASH,
        ]);

    $order = Order::latest()->first();
    $payment = Payment::latest()->first();

    $this->assertEquals('pending', $order->fresh()->status);
    $this->assertEquals('pending', $order->fresh()->payment_status); // Paid on delivery
    $this->assertEquals(Payment::STATUS_PENDING, $payment->fresh()->status);
});

test('checkout blocks payment methods disabled by admin', function () {
    // Disable Yape
    SystemSetting::updateOrCreate(
        ['key' => 'payment_method_yape_active'],
        ['value' => '0']
    );

    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $response = $this->actingAs($this->customer)
        ->from(route('checkout.index'))
        ->post(route('checkout.store'), [
            'address_selection_type' => 'saved',
            'customer_address_id' => $this->address->id,
            'payment_method' => Payment::METHOD_YAPE, // Try to checkout with disabled Yape
        ]);

    $response->assertRedirect(route('checkout.index'));
    $response->assertSessionHasErrors('payment_method');
});

test('admin can toggle payment method configurations', function () {
    // Assert defaults to true
    $this->actingAs($this->admin)
        ->get(route('admin.settings.payments.edit'))
        ->assertStatus(200);

    // Disable plin
    $response = $this->actingAs($this->admin)
        ->post(route('admin.settings.payments.update'), [
            'methods' => [
                'cash' => '1',
                'card' => '1',
                'yape' => '1',
                'plin' => '0',
                'bank_transfer' => '1',
                'pagoefectivo' => '1',
            ],
        ]);

    $response->assertRedirect(route('admin.settings.payments.edit'));

    $this->assertDatabaseHas('system_settings', [
        'key' => 'payment_method_plin_active',
        'value' => '0',
    ]);
});

test('authenticated users can place order using new address and save it to address book', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    // Count initial customer addresses
    $initialCount = CustomerAddress::where('user_id', $this->customer->id)->count();

    $response = $this->actingAs($this->customer)
        ->post(route('checkout.store'), [
            'address_selection_type' => 'new',
            'new_label' => 'Casa de Verano',
            'new_address' => 'Av. Larco 123',
            'new_address_type' => 'other',
            'new_reference' => 'Cerca al parque Salazar',
            'new_delivery_notes' => 'Timbre dañado',
            'new_district' => 'Miraflores',
            'new_province' => 'Lima',
            'new_department' => 'Lima',
            'new_postal_code' => '15074',
            'new_contact_name' => 'Juan Perez',
            'new_contact_phone' => '999888777',
            'new_latitude' => -12.122,
            'new_longitude' => -77.029,
            'save_address' => '1',
            'payment_method' => Payment::METHOD_CASH,
        ]);

    $order = Order::latest()->first();
    $response->assertRedirect(route('checkout.success', $order));

    // Verify address book grew by 1
    $this->assertEquals($initialCount + 1, CustomerAddress::where('user_id', $this->customer->id)->count());

    // Verify the address was created and has the expected values
    $newAddress = CustomerAddress::where('user_id', $this->customer->id)->where('label', 'Casa de Verano')->first();
    $this->assertNotNull($newAddress);
    $this->assertEquals('Av. Larco 123', $newAddress->address);
    $this->assertEquals('other', $newAddress->address_type);
    $this->assertEquals(-12.122, $newAddress->latitude);
    $this->assertEquals(-77.029, $newAddress->longitude);

    // Verify the order links to this saved address and has correct snapshots
    $this->assertEquals($newAddress->id, $order->customer_address_id);
    $this->assertEquals('Av. Larco 123', $order->delivery_address);
    $this->assertEquals(-12.122, $order->delivery_latitude);
    $this->assertEquals(-77.029, $order->delivery_longitude);
});

test('authenticated users can place order using new address without saving it to address book', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    // Count initial customer addresses
    $initialCount = CustomerAddress::where('user_id', $this->customer->id)->count();

    $response = $this->actingAs($this->customer)
        ->post(route('checkout.store'), [
            'address_selection_type' => 'new',
            'new_label' => 'Trabajo Temporal',
            'new_address' => 'Av. Javier Prado 456',
            'new_address_type' => 'work',
            'new_reference' => 'Piso 15',
            'new_delivery_notes' => 'Dejar en recepción',
            'new_district' => 'San Isidro',
            'new_province' => 'Lima',
            'new_department' => 'Lima',
            'new_postal_code' => '15046',
            'new_contact_name' => 'Maria Gomez',
            'new_contact_phone' => '987654321',
            'new_latitude' => -12.095,
            'new_longitude' => -77.032,
            'save_address' => '0',
            'payment_method' => Payment::METHOD_CASH,
        ]);

    $order = Order::latest()->first();
    $response->assertRedirect(route('checkout.success', $order));

    // Verify address book DID NOT grow
    $this->assertEquals($initialCount, CustomerAddress::where('user_id', $this->customer->id)->count());

    // Verify the address was NOT created in customer_addresses
    $this->assertNull(CustomerAddress::where('user_id', $this->customer->id)->where('label', 'Trabajo Temporal')->first());

    // Verify the order has no customer_address_id (null) but has correct snapshot details
    $this->assertNull($order->customer_address_id);
    $this->assertEquals('Av. Javier Prado 456', $order->delivery_address);
    $this->assertEquals('Piso 15', $order->delivery_reference);
    $this->assertEquals(-12.095, $order->delivery_latitude);
    $this->assertEquals(-77.032, $order->delivery_longitude);
});

test('checkout fails when saved address is in an inactive district', function () {
    // Disable Miraflores (only Chiclayo active)
    SystemSetting::updateOrCreate(
        ['key' => 'active_districts'],
        ['value' => json_encode(['Lambayeque|Chiclayo|Chiclayo'])]
    );

    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    // This address is in Miraflores, which is now inactive
    $response = $this->actingAs($this->customer)
        ->post(route('checkout.store'), [
            'address_selection_type' => 'saved',
            'customer_address_id' => $this->address->id,
            'payment_method' => Payment::METHOD_CASH,
        ]);

    $response->assertStatus(400); // Because store method aborts with 400
});

test('checkout fails when new address is in an inactive district', function () {
    // Enable only Chiclayo
    SystemSetting::updateOrCreate(
        ['key' => 'active_districts'],
        ['value' => json_encode(['Lambayeque|Chiclayo|Chiclayo'])]
    );

    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $response = $this->actingAs($this->customer)
        ->from(route('checkout.index'))
        ->post(route('checkout.store'), [
            'address_selection_type' => 'new',
            'new_label' => 'Casa Temporal',
            'new_address' => 'Calle Falsa 123',
            'new_district' => 'Miraflores', // Inactive district
            'payment_method' => Payment::METHOD_CASH,
        ]);

    $response->assertRedirect(route('checkout.index'));
    $response->assertSessionHasErrors('new_district');
});
