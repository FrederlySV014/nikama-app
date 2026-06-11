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

    // Roles
    $this->superAdminRole = Role::where('slug', Role::SUPER_ADMIN)->first();
    $this->sellerRole = Role::where('slug', Role::SELLER)->first();
    $this->customerRole = Role::where('slug', Role::CUSTOMER)->first();

    // Super Admin User
    $this->admin = User::factory()->create();
    $this->admin->roles()->attach($this->superAdminRole->id);

    // Seller User
    $this->seller = User::factory()->create();
    $this->seller->roles()->attach($this->sellerRole->id);

    // Normal Business managed by Seller
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

    // Customer User
    $this->customer = User::factory()->create();
    $this->customer->roles()->attach($this->customerRole->id);

    // Categories
    $this->rootCategory = Category::factory()->create([
        'name' => 'Alimentos',
        'is_active' => true,
        'parent_id' => null,
    ]);

    $this->leafCategory = Category::factory()->create([
        'name' => 'Bebidas',
        'is_active' => true,
        'parent_id' => $this->rootCategory->id,
    ]);
});

test('guests are redirected to login when accessing products list', function () {
    $this->get(route('admin.products.index'))->assertRedirect(route('login'));
});

test('non-admin users cannot access admin products list', function () {
    $this->actingAs($this->customer)
        ->get(route('admin.products.index'))
        ->assertStatus(403);

    $this->actingAs($this->seller)
        ->get(route('admin.products.index'))
        ->assertStatus(403);
});

test('super admin can access products index', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.products.index'));

    $response->assertStatus(200)
        ->assertViewIs('admin.products.index')
        ->assertViewHasAll(['products', 'businesses', 'search', 'status', 'businessFilter', 'featuredFilter']);
});

test('super admin can see products belonging to all businesses', function () {
    $product1 = Product::factory()->create([
        'business_id' => $this->business->id,
        'name' => 'Seller Ice Cream',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.products.index'));

    $response->assertSee('Seller Ice Cream');
});

test('super admin can toggle status of any product', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.products.toggle', $product));

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'status' => 'inactive',
    ]);
});

test('super admin can toggle featured status of any product', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business->id,
        'is_featured' => false,
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.products.toggle-featured', $product));

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'is_featured' => true,
    ]);
});

test('CRUD actions (create, store, edit, update, delete) do not exist for admin products', function () {
    $product = Product::factory()->create([
        'business_id' => $this->business->id,
    ]);

    // Test that endpoints are not defined and return 404 or 405
    $this->actingAs($this->admin)
        ->get('/admin/products/create')
        ->assertStatus(404);

    $this->actingAs($this->admin)
        ->post('/admin/products')
        ->assertStatus(405); // Method Not Allowed (GET exists)

    $this->actingAs($this->admin)
        ->get("/admin/products/{$product->id}/edit")
        ->assertStatus(404);

    $this->actingAs($this->admin)
        ->put("/admin/products/{$product->id}")
        ->assertStatus(404);

    $this->actingAs($this->admin)
        ->delete("/admin/products/{$product->id}")
        ->assertStatus(404);
});
