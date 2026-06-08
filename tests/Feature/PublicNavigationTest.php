<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $this->customer = User::factory()->create();
    $this->customer->roles()->attach($this->customerRole->id);

    // Create a business that is approved and active
    $this->business = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);

    // Categories
    $this->parentCategory = Category::factory()->create([
        'name' => 'Farmacia',
        'slug' => 'farmacia',
        'is_active' => true,
        'parent_id' => null,
    ]);

    $this->subCategory = Category::factory()->create([
        'name' => 'Medicamentos',
        'slug' => 'medicamentos',
        'is_active' => true,
        'parent_id' => $this->parentCategory->id,
    ]);

    // Product inside subcategory
    $this->product = Product::factory()->create([
        'business_id' => $this->business->id,
        'name' => 'Paracetamol 500mg',
        'slug' => 'paracetamol-500mg',
        'price' => 5.50,
        'compare_price' => 6.00,
        'status' => Product::STATUS_ACTIVE,
        'is_available' => true,
    ]);

    $this->product->categories()->attach($this->subCategory->id, [
        'id' => Str::uuid()->toString(),
        'sort_order' => 0,
    ]);
});

test('guests can access category show page', function () {
    $response = $this->get(route('public.category.show', $this->parentCategory->url_path));

    $response->assertSuccessful()
        ->assertViewIs('public.categories.show')
        ->assertViewHas('category')
        ->assertSee('Farmacia');
});

test('category show page displays products from subcategories recursively', function () {
    // Parent category page should display the product from its subcategory
    $response = $this->get(route('public.category.show', $this->parentCategory->url_path));

    $response->assertSuccessful()
        ->assertSee('Paracetamol 500mg');
});

test('guests can access product details page', function () {
    $response = $this->get(route('public.product.show', $this->product->slug));

    $response->assertSuccessful()
        ->assertViewIs('public.products.show')
        ->assertViewHas('product')
        ->assertSee('Paracetamol 500mg')
        ->assertSee('Medicamentos')
        ->assertSee('Vendido por')
        ->assertSee($this->business->business_name);
});

test('non-authenticated users cannot submit reviews', function () {
    $response = $this->post(route('public.product.review.store', $this->product), [
        'rating' => 5,
        'comment' => 'Excelente producto',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseEmpty('product_reviews');
});

test('authenticated customers can submit reviews and rating average is updated', function () {
    $response = $this->actingAs($this->customer)
        ->post(route('public.product.review.store', $this->product), [
            'rating' => 5,
            'comment' => 'Excelente producto',
        ]);

    $response->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('product_reviews', [
        'product_id' => $this->product->id,
        'user_id' => $this->customer->id,
        'rating' => 5,
        'comment' => 'Excelente producto',
    ]);

    // Check that product rating average and total reviews were recalculated
    $this->product->refresh();
    expect($this->product->rating_average)->toEqual(5.0);
    expect($this->product->total_reviews)->toEqual(1);
});

test('authenticated customers cannot review the same product twice', function () {
    // Submit first review
    $this->actingAs($this->customer)
        ->post(route('public.product.review.store', $this->product), [
            'rating' => 5,
            'comment' => 'Excelente producto',
        ]);

    // Submit second review
    $response = $this->actingAs($this->customer)
        ->from(route('public.product.show', $this->product->slug))
        ->post(route('public.product.review.store', $this->product), [
            'rating' => 4,
            'comment' => 'Otro comentario',
        ]);

    $response->assertRedirect(route('public.product.show', $this->product->slug))
        ->assertSessionHas('error');

    // Ensure only 1 review exists
    expect($this->product->reviews()->count())->toEqual(1);
});

test('validation errors are returned for invalid ratings', function () {
    $response = $this->actingAs($this->customer)
        ->from(route('public.product.show', $this->product->slug))
        ->post(route('public.product.review.store', $this->product), [
            'rating' => 6, // Invalid rating (max is 5)
            'comment' => 'Buenisimo',
        ]);

    $response->assertRedirect(route('public.product.show', $this->product->slug))
        ->assertSessionHasErrors('rating');

    $this->assertDatabaseEmpty('product_reviews');
});
