<?php

use App\Models\Business;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\CustomerAddress;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionGroup;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Create a customer user
    $this->customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $this->customer->roles()->attach($customerRole->id);

    // Seed active districts
    SystemSetting::updateOrCreate(
        ['key' => 'active_districts'],
        ['value' => json_encode([
            'Lambayeque|Chiclayo|Chiclayo',
            'Lambayeque|Chiclayo|La Victoria',
            'Lima|Lima|Miraflores',
            'Lima|Lima|San Isidro',
        ])]
    );

    // Create a business and vendor
    $this->business = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);
});

test('api categories returns active categories', function () {
    $category = Category::create([
        'name' => 'Comida',
        'slug' => 'comida',
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/v1/categories');

    $response->assertSuccessful();
    $response->assertJsonFragment(['name' => 'Comida']);
});

test('api products can be retrieved and filtered', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business->id,
        'name' => 'Hamburguesa Nikama',
        'status' => Product::STATUS_ACTIVE,
        'is_available' => true,
        'price' => 15.90,
    ]);

    $response = $this->getJson('/api/v1/products');

    $response->assertSuccessful();
    $response->assertJsonFragment(['name' => 'Hamburguesa Nikama']);
});

test('api product detail includes option groups and options', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business->id,
        'status' => Product::STATUS_ACTIVE,
        'is_available' => true,
    ]);

    $group = ProductOptionGroup::create([
        'product_id' => $product->id,
        'name' => 'Extras',
        'min_selectable' => 0,
        'max_selectable' => 3,
        'is_required' => false,
    ]);

    $option = ProductOption::create([
        'product_option_group_id' => $group->id,
        'name' => 'Queso Extra',
        'additional_price' => 2.50,
        'is_available' => true,
    ]);

    $response = $this->getJson("/api/v1/products/{$product->id}");

    $response->assertSuccessful();
    $response->assertJsonFragment(['name' => 'Queso Extra']);
});

test('api cart show, add, update, and remove items', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business->id,
        'status' => Product::STATUS_ACTIVE,
        'is_available' => true,
        'price' => 10.00,
    ]);

    // 1. Show empty cart
    $response = $this->actingAs($this->customer, 'sanctum')->getJson('/api/v1/customer/cart');
    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'data' => [
            'items' => [],
            'subtotal' => 0,
        ],
    ]);

    // 2. Add item to cart
    $response = $this->actingAs($this->customer, 'sanctum')->postJson('/api/v1/customer/cart/add', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertSuccessful();
    $response->assertJsonPath('data.subtotal', 20);
    $response->assertJsonCount(1, 'data.items');

    $itemId = $response->json('data.items.0.id');

    // 3. Update quantity
    $response = $this->actingAs($this->customer, 'sanctum')->putJson("/api/v1/customer/cart/items/{$itemId}", [
        'quantity' => 3,
    ]);

    $response->assertSuccessful();
    $response->assertJsonPath('data.subtotal', 30);

    // 4. Remove item
    $response = $this->actingAs($this->customer, 'sanctum')->deleteJson("/api/v1/customer/cart/items/{$itemId}");

    $response->assertSuccessful();
    $response->assertJsonPath('data.subtotal', 0);
});

test('api customer address crud', function () {
    // 1. Create address
    $response = $this->actingAs($this->customer, 'sanctum')->postJson('/api/v1/customer/addresses', [
        'label' => 'Casa',
        'address' => 'Av. Larco 123',
        'district' => 'Miraflores',
        'province' => 'Lima',
        'department' => 'Lima',
        'latitude' => -12.1234,
        'longitude' => -77.0123,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('customer_addresses', [
        'user_id' => $this->customer->id,
        'label' => 'Casa',
        'is_default' => true,
    ]);

    $addressId = $response->json('data.id');

    // 2. Update address
    $response = $this->actingAs($this->customer, 'sanctum')->putJson("/api/v1/customer/addresses/{$addressId}", [
        'label' => 'Trabajo',
        'address' => 'Av. Larco 456',
        'district' => 'Miraflores',
        'province' => 'Lima',
        'department' => 'Lima',
        'is_default' => false,
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('customer_addresses', [
        'id' => $addressId,
        'label' => 'Trabajo',
    ]);

    // 3. Delete address
    $response = $this->actingAs($this->customer, 'sanctum')->deleteJson("/api/v1/customer/addresses/{$addressId}");

    $response->assertSuccessful();
    $this->assertSoftDeleted('customer_addresses', ['id' => $addressId]);
});

test('api checkout places order successfully', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business->id,
        'status' => Product::STATUS_ACTIVE,
        'is_available' => true,
        'price' => 20.00,
        'track_stock' => true,
        'stock_quantity' => 10,
    ]);

    // Add to cart
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 20.00,
    ]);

    // Save Address
    $address = CustomerAddress::create([
        'user_id' => $this->customer->id,
        'label' => 'Casa',
        'address' => 'Av. Larco 123',
        'district' => 'Miraflores',
        'province' => 'Lima',
        'department' => 'Lima',
        'is_default' => true,
        'is_active' => true,
    ]);

    // Mock payment method active settings
    SystemSetting::updateOrCreate(
        ['key' => 'payment_method_cash_active'],
        ['value' => '1']
    );

    // Perform Checkout
    $response = $this->actingAs($this->customer, 'sanctum')->postJson('/api/v1/customer/checkout', [
        'address_selection_type' => 'saved',
        'customer_address_id' => $address->id,
        'payment_method' => 'cash',
        'notes' => 'Llamar antes de entregar',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.status', 'pending');
    $response->assertJsonPath('data.total', 45); // 40 subtotal + 5 delivery fee

    // Verify stock decremented
    $this->assertEquals(8, $product->fresh()->stock_quantity);

    // Verify cart converted
    $this->assertEquals(Cart::STATUS_CONVERTED, $cart->fresh()->status);
});
