<?php

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CustomerAddress;
use App\Models\Product;
use App\Models\ProductCombo;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Roles
    $this->sellerRole = Role::where('slug', Role::SELLER)->first();
    $this->customerRole = Role::where('slug', Role::CUSTOMER)->first();

    // Seller 1
    $this->seller = User::factory()->create();
    $this->seller->roles()->attach($this->sellerRole->id);

    $this->business = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);

    BusinessUser::create([
        'business_id' => $this->business->id,
        'user_id' => $this->seller->id,
        'role' => BusinessUser::ROLE_ADMIN,
        'is_active' => true,
        'joined_at' => now(),
    ]);

    // Seller 2 (Other Seller)
    $this->otherSeller = User::factory()->create();
    $this->otherSeller->roles()->attach($this->sellerRole->id);

    $this->otherBusiness = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);

    BusinessUser::create([
        'business_id' => $this->otherBusiness->id,
        'user_id' => $this->otherSeller->id,
        'role' => BusinessUser::ROLE_ADMIN,
        'is_active' => true,
        'joined_at' => now(),
    ]);

    // Customer
    $this->customer = User::factory()->create();
    $this->customer->roles()->attach($this->customerRole->id);

    // Products
    $this->product1 = Product::factory()->create([
        'business_id' => $this->business->id,
        'price' => 10.00,
        'status' => Product::STATUS_ACTIVE,
        'track_stock' => true,
        'stock_quantity' => 100,
    ]);

    $this->product2 = Product::factory()->create([
        'business_id' => $this->business->id,
        'price' => 15.00,
        'status' => Product::STATUS_ACTIVE,
        'track_stock' => true,
        'stock_quantity' => 50,
    ]);

    // Other seller product
    $this->otherProduct = Product::factory()->create([
        'business_id' => $this->otherBusiness->id,
        'price' => 20.00,
        'status' => Product::STATUS_ACTIVE,
    ]);

    // System Settings for checkout
    SystemSetting::updateOrCreate(
        ['key' => 'active_districts'],
        ['value' => json_encode(['Lima|Lima|Miraflores'])]
    );

    // Address
    $this->address = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'department' => 'Lima',
        'province' => 'Lima',
        'district' => 'Miraflores',
        'is_default' => true,
        'is_active' => true,
    ]);
});

test('guests are redirected to login when managing combos', function () {
    $this->get(route('seller.combos.index'))->assertRedirect(route('login'));
    $this->get(route('seller.combos.create'))->assertRedirect(route('login'));
});

test('non-seller users cannot access combo management', function () {
    $this->actingAs($this->customer)
        ->get(route('seller.combos.index'))
        ->assertStatus(403);
});

test('sellers can access combo index and see their combos', function () {
    $combo = ProductCombo::create([
        'business_id' => $this->business->id,
        'name' => 'Mi Combo 1',
        'price' => 22.00,
        'is_active' => true,
    ]);

    $otherCombo = ProductCombo::create([
        'business_id' => $this->otherBusiness->id,
        'name' => 'Combo Ajeno',
        'price' => 30.00,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->seller)
        ->get(route('seller.combos.index'));

    $response->assertStatus(200)
        ->assertViewIs('seller.combos.index')
        ->assertSee('Mi Combo 1')
        ->assertDontSee('Combo Ajeno');
});

test('sellers can create combos with their products', function () {
    $response = $this->actingAs($this->seller)
        ->post(route('seller.combos.store'), [
            'name' => 'Combo Super Especial',
            'business_id' => $this->business->id,
            'description' => 'Un combo fabuloso',
            'price' => 19.99,
            'is_active' => true,
            'products' => [
                ['product_id' => $this->product1->id, 'quantity' => 2],
                ['product_id' => $this->product2->id, 'quantity' => 1],
            ],
        ]);

    $response->assertRedirect(route('seller.combos.index'));

    $this->assertDatabaseHas('product_combos', [
        'name' => 'Combo Super Especial',
        'business_id' => $this->business->id,
        'price' => 19.99,
    ]);

    $combo = ProductCombo::where('name', 'Combo Super Especial')->first();

    $this->assertDatabaseHas('product_combo_items', [
        'product_combo_id' => $combo->id,
        'product_id' => $this->product1->id,
        'quantity' => 2,
    ]);

    $this->assertDatabaseHas('product_combo_items', [
        'product_combo_id' => $combo->id,
        'product_id' => $this->product2->id,
        'quantity' => 1,
    ]);
});

test('sellers cannot create combos with products of other businesses', function () {
    $response = $this->actingAs($this->seller)
        ->from(route('seller.combos.create'))
        ->post(route('seller.combos.store'), [
            'name' => 'Combo Super Especial',
            'business_id' => $this->business->id,
            'description' => 'Un combo fabuloso',
            'price' => 19.99,
            'is_active' => true,
            'products' => [
                ['product_id' => $this->otherProduct->id, 'quantity' => 1],
            ],
        ]);

    $response->assertRedirect(route('seller.combos.create'));
    $response->assertSessionHasErrors('products.0.product_id');
});

test('sellers can edit and update their combos', function () {
    $combo = ProductCombo::create([
        'business_id' => $this->business->id,
        'name' => 'Combo Viejo',
        'price' => 25.00,
        'is_active' => true,
    ]);

    $combo->products()->attach($this->product1->id, [
        'id' => Str::uuid()->toString(),
        'quantity' => 1,
    ]);

    $response = $this->actingAs($this->seller)
        ->put(route('seller.combos.update', $combo), [
            'name' => 'Combo Actualizado',
            'business_id' => $this->business->id,
            'price' => 21.50,
            'is_active' => true,
            'products' => [
                ['product_id' => $this->product2->id, 'quantity' => 3],
            ],
        ]);

    $response->assertRedirect(route('seller.combos.index'));

    $this->assertDatabaseHas('product_combos', [
        'id' => $combo->id,
        'name' => 'Combo Actualizado',
        'price' => 21.50,
    ]);

    // Check old item was synced/removed and new one added
    $this->assertDatabaseMissing('product_combo_items', [
        'product_combo_id' => $combo->id,
        'product_id' => $this->product1->id,
    ]);

    $this->assertDatabaseHas('product_combo_items', [
        'product_combo_id' => $combo->id,
        'product_id' => $this->product2->id,
        'quantity' => 3,
    ]);
});

test('sellers cannot edit combos of other businesses', function () {
    $combo = ProductCombo::create([
        'business_id' => $this->otherBusiness->id,
        'name' => 'Combo Ajeno',
        'price' => 30.00,
    ]);

    $response = $this->actingAs($this->seller)
        ->put(route('seller.combos.update', $combo), [
            'name' => 'Combo Hack',
            'business_id' => $this->business->id,
            'price' => 10.00,
            'products' => [
                ['product_id' => $this->product1->id, 'quantity' => 1],
            ],
        ]);

    $response->assertStatus(403);
});

test('sellers can toggle status of their combos', function () {
    $combo = ProductCombo::create([
        'business_id' => $this->business->id,
        'name' => 'Combo Activo',
        'price' => 10.00,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->seller)
        ->post(route('seller.combos.toggle', $combo));

    $response->assertRedirect();

    $this->assertDatabaseHas('product_combos', [
        'id' => $combo->id,
        'is_active' => false,
    ]);
});

test('sellers can delete their combos', function () {
    $combo = ProductCombo::create([
        'business_id' => $this->business->id,
        'name' => 'Combo Para Borrar',
        'price' => 10.00,
    ]);

    $response = $this->actingAs($this->seller)
        ->delete(route('seller.combos.destroy', $combo));

    $response->assertRedirect(route('seller.combos.index'));

    $this->assertDatabaseMissing('product_combos', [
        'id' => $combo->id,
    ]);
});

test('customers can add combos to cart', function () {
    $combo = ProductCombo::create([
        'business_id' => $this->business->id,
        'name' => 'Combo Familiar',
        'price' => 20.00,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->customer)
        ->post(route('cart.add'), [
            'product_combo_id' => $combo->id,
            'quantity' => 2,
        ]);

    $response->assertRedirect();

    $cart = Cart::where('user_id', $this->customer->id)->first();
    expect($cart)->not->toBeNull();

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'product_combo_id' => $combo->id,
        'product_id' => null,
        'quantity' => 2,
        'unit_price' => 20.00,
    ]);
});

test('checkout reduces component products stock correctly when buying combos', function () {
    $combo = ProductCombo::create([
        'business_id' => $this->business->id,
        'name' => 'Mega Combo',
        'price' => 25.00,
        'is_active' => true,
    ]);

    $combo->products()->attach($this->product1->id, [
        'id' => Str::uuid()->toString(),
        'quantity' => 3,
    ]);

    $combo->products()->attach($this->product2->id, [
        'id' => Str::uuid()->toString(),
        'quantity' => 1,
    ]);

    // Active cart
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_combo_id' => $combo->id,
        'product_id' => null,
        'quantity' => 2, // Ordering 2 combos
        'unit_price' => 25.00,
    ]);

    // Check initial stock
    expect($this->product1->fresh()->stock_quantity)->toEqual(100);
    expect($this->product2->fresh()->stock_quantity)->toEqual(50);

    // Call checkout store
    $response = $this->actingAs($this->customer)
        ->post(route('checkout.store'), [
            'address_selection_type' => 'saved',
            'customer_address_id' => $this->address->id,
            'payment_method' => 'cash',
            'notes' => 'Tocar timbre fuerte',
        ]);

    $response->assertRedirect();

    // Verify order is created and cart converted
    $this->assertDatabaseHas('orders', [
        'user_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    expect($cart->fresh()->status)->toEqual(Cart::STATUS_CONVERTED);

    // Verify stock is decremented:
    // Product 1: 100 - (3 * 2) = 94
    // Product 2: 50 - (1 * 2) = 48
    expect($this->product1->fresh()->stock_quantity)->toEqual(94);
    expect($this->product2->fresh()->stock_quantity)->toEqual(48);
});
