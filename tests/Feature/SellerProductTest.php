<?php

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Create Roles
    $this->sellerRole = Role::where('slug', Role::SELLER)->first();
    $this->customerRole = Role::where('slug', Role::CUSTOMER)->first();

    // Create Seller 1 (with business 1, approved)
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

    // Create Seller 2 (with business 2, approved)
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

    // Create a generic Customer
    $this->customer = User::factory()->create();
    $this->customer->roles()->attach($this->customerRole->id);

    // Create Category structure
    $this->rootCategory = Category::factory()->create([
        'name' => 'Farmacia',
        'is_active' => true,
        'parent_id' => null,
    ]);

    $this->leafCategory = Category::factory()->create([
        'name' => 'Medicamentos',
        'is_active' => true,
        'parent_id' => $this->rootCategory->id,
    ]);
});

test('guests are redirected to login when managing products', function () {
    $this->get(route('seller.products.index'))->assertRedirect(route('login'));
    $this->get(route('seller.products.create'))->assertRedirect(route('login'));
});

test('non-seller users cannot access product management', function () {
    $this->actingAs($this->customer)
        ->get(route('seller.products.index'))
        ->assertStatus(403);
});

test('sellers can access their products index', function () {
    $response = $this->actingAs($this->seller1)
        ->get(route('seller.products.index'));

    $response->assertStatus(200)
        ->assertViewIs('seller.products.index')
        ->assertViewHasAll(['products', 'businesses', 'search', 'status', 'businessFilter']);
});

test('sellers can only see products belonging to their businesses', function () {
    // Product for Business 1
    $product1 = Product::factory()->create([
        'business_id' => $this->business1->id,
        'name' => 'Product Business 1',
    ]);

    // Product for Business 2
    $product2 = Product::factory()->create([
        'business_id' => $this->business2->id,
        'name' => 'Product Business 2',
    ]);

    // Seller 1 should see product 1 but not product 2
    $response = $this->actingAs($this->seller1)
        ->get(route('seller.products.index'));

    $response->assertSee('Product Business 1')
        ->assertDontSee('Product Business 2');
});

test('sellers can create a product in their business with a leaf category', function () {
    $response = $this->actingAs($this->seller1)
        ->post(route('seller.products.store'), [
            'name' => 'Nuevo Remedio',
            'business_id' => $this->business1->id,
            'price' => 12.50,
            'status' => 'active',
            'category_id' => $this->leafCategory->id,
            'track_stock' => false,
        ]);

    $response->assertRedirect(route('seller.products.index'));

    $this->assertDatabaseHas('products', [
        'name' => 'Nuevo Remedio',
        'business_id' => $this->business1->id,
        'price' => 12.50,
    ]);
});

test('sellers cannot create a product using a non-leaf category', function () {
    // $this->rootCategory has a child ($this->leafCategory), so it is not a leaf node
    $response = $this->actingAs($this->seller1)
        ->from(route('seller.products.create'))
        ->post(route('seller.products.store'), [
            'name' => 'Producto Invalido',
            'business_id' => $this->business1->id,
            'price' => 10.00,
            'status' => 'draft',
            'category_id' => $this->rootCategory->id,
            'track_stock' => false,
        ]);

    $response->assertRedirect(route('seller.products.create'));
    $response->assertSessionHasErrors('category_id');
});

test('sellers cannot create a product in a business they do not own', function () {
    $response = $this->actingAs($this->seller1)
        ->post(route('seller.products.store'), [
            'name' => 'Producto Ajeno',
            'business_id' => $this->business2->id, // Seller 1 does not own business 2
            'price' => 15.00,
            'status' => 'draft',
            'category_id' => $this->leafCategory->id,
            'track_stock' => false,
        ]);

    $response->assertSessionHasErrors('business_id');
});

test('sellers can edit their own products', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business1->id,
        'name' => 'Antiguo Nombre',
    ]);

    $response = $this->actingAs($this->seller1)
        ->put(route('seller.products.update', $product), [
            'name' => 'Nombre Editado',
            'business_id' => $this->business1->id,
            'price' => 20.00,
            'status' => 'active',
            'category_id' => $this->leafCategory->id,
            'track_stock' => false,
        ]);

    $response->assertRedirect(route('seller.products.index'));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Nombre Editado',
    ]);
});

test('sellers cannot edit products of other sellers', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business2->id,
        'name' => 'Producto Vendedor 2',
    ]);

    $response = $this->actingAs($this->seller1)
        ->put(route('seller.products.update', $product), [
            'name' => 'Ataque Hack',
            'business_id' => $this->business2->id,
            'price' => 5.00,
            'status' => 'active',
            'category_id' => $this->leafCategory->id,
        ]);

    $response->assertStatus(403);
});

test('sellers can toggle status of their products', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business1->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->seller1)
        ->post(route('seller.products.toggle', $product));

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'status' => 'inactive',
    ]);
});

test('sellers can soft-delete their products', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business1->id,
    ]);

    $response = $this->actingAs($this->seller1)
        ->delete(route('seller.products.destroy', $product));

    $response->assertRedirect(route('seller.products.index'));

    $this->assertSoftDeleted('products', [
        'id' => $product->id,
    ]);
});
