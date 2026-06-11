<?php

use App\Models\Business;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Role;
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

    // Create a business
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
});

test('guests are redirected when modifying cart but guests receive empty cart json', function () {
    // Add to cart
    $this->post(route('cart.add'), [
        'product_id' => $this->product->id,
        'quantity' => 1,
    ])->assertRedirect(route('login'));

    // Get JSON
    $this->get(route('cart.json'))
        ->assertOk()
        ->assertJson([
            'items' => [],
            'subtotal' => 0.00,
            'items_count' => 0,
        ]);
});

test('authenticated users can add a product to the cart', function () {
    $response = $this->actingAs($this->customer)
        ->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

    $response->assertRedirect();

    $cart = Cart::where('user_id', $this->customer->id)->first();
    $this->assertNotNull($cart);
    $this->assertEquals(Cart::STATUS_ACTIVE, $cart->status);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'product_id' => $this->product->id,
        'quantity' => 2,
        'unit_price' => 15.00,
    ]);
});

test('authenticated users adding the same product increments quantity', function () {
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
        ->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'product_id' => $this->product->id,
        'quantity' => 4,
    ]);
});

test('authenticated users can update quantity of a cart item', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    $item = CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $response = $this->actingAs($this->customer)
        ->post(route('cart.quantity.update', $item), [
            'quantity' => 5,
        ]);

    $response->assertRedirect();
    $this->assertEquals(5, $item->fresh()->quantity);
});

test('authenticated users updating quantity to 0 deletes the cart item', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    $item = CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $response = $this->actingAs($this->customer)
        ->post(route('cart.quantity.update', $item), [
            'quantity' => 0,
        ]);

    $this->assertDatabaseMissing('cart_items', [
        'id' => $item->id,
    ]);
});

test('authenticated users can remove an item from the cart', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    $item = CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $response = $this->actingAs($this->customer)
        ->delete(route('cart.remove', $item));

    $response->assertRedirect();
    $this->assertDatabaseMissing('cart_items', [
        'id' => $item->id,
    ]);
});

test('authenticated users cannot modify another users cart item', function () {
    $cart = Cart::create([
        'user_id' => $this->customer->id,
        'status' => Cart::STATUS_ACTIVE,
    ]);

    $item = CartItem::create([
        'cart_id' => $cart->id,
        'business_id' => $this->business->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $otherUser = User::factory()->create();
    $otherRole = Role::where('slug', Role::CUSTOMER)->first();
    $otherUser->roles()->attach($otherRole->id);

    $this->actingAs($otherUser)
        ->post(route('cart.quantity.update', $item), [
            'quantity' => 5,
        ])->assertStatus(403);

    $this->actingAs($otherUser)
        ->delete(route('cart.remove', $item))
        ->assertStatus(403);
});

test('authenticated users can retrieve cart json', function () {
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
        ->getJson(route('cart.json'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'items' => [
                '*' => [
                    'id',
                    'product_id',
                    'product_name',
                    'product_image',
                    'business_name',
                    'quantity',
                    'unit_price',
                    'total_price',
                ],
            ],
            'subtotal',
            'items_count',
        ])
        ->assertJson([
            'subtotal' => 30.00,
            'items_count' => 2,
        ]);
});
