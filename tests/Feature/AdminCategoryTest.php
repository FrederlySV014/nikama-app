<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Crear un Super Admin
    $this->admin = User::factory()->create();
    $adminRole = Role::where('slug', Role::SUPER_ADMIN)->first();
    $this->admin->roles()->attach($adminRole->id);

    // Crear un Customer (no admin)
    $this->customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $this->customer->roles()->attach($customerRole->id);
});

test('guests are redirected to login when accessing categories', function () {
    $this->get(route('admin.categories.index'))
        ->assertRedirect(route('login'));
});

test('non-admin users cannot access categories', function () {
    $this->actingAs($this->customer)
        ->get(route('admin.categories.index'))
        ->assertStatus(403);
});

test('super admin can access categories index', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.categories.index'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.categories.index');
    $response->assertViewHasAll([
        'categories',
        'search',
        'status',
        'level',
        'parentFilter',
        'totalCategoriesCount',
        'activeCategoriesCount',
        'inactiveCategoriesCount',
        'rootCategoriesCount',
        'childCategoriesCount',
        'parentCategories'
    ]);
});

test('super admin can create a root category', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.categories.store'), [
            'name' => 'Farmacias',
            'slug' => 'farmacias',
            'description' => 'Medicamentos y salud',
            'icon' => '💊',
            'sort_order' => 1,
            'is_active' => true,
        ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'name' => 'Farmacias',
        'slug' => 'farmacias',
        'parent_id' => null,
        'icon' => '💊',
    ]);
});

test('super admin can create a child category', function () {
    $parent = Category::factory()->create([
        'name' => 'Restaurantes',
        'slug' => 'restaurantes',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.categories.store'), [
            'name' => 'Hamburguesas',
            'slug' => 'hamburguesas',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'name' => 'Hamburguesas',
        'slug' => 'hamburguesas',
        'parent_id' => $parent->id,
    ]);
});

test('validates slug unique and format constraints', function () {
    Category::factory()->create([
        'slug' => 'farmacias',
    ]);

    // Duplicado
    $response = $this->actingAs($this->admin)
        ->post(route('admin.categories.store'), [
            'name' => 'Farmacias 2',
            'slug' => 'farmacias',
            'is_active' => true,
        ]);

    $response->assertSessionHasErrors('slug');

    // Formato inválido (con mayúsculas o caracteres prohibidos)
    $response2 = $this->actingAs($this->admin)
        ->post(route('admin.categories.store'), [
            'name' => 'Medicamentos',
            'slug' => 'Medicamentos_inválidos!',
            'is_active' => true,
        ]);

    $response2->assertSessionHasErrors('slug');
});

test('prevents circular parent-child reference to self', function () {
    $category = Category::factory()->create([
        'name' => 'Tecnología',
        'slug' => 'tecnologia',
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.categories.update', $category), [
            'name' => 'Tecnología',
            'slug' => 'tecnologia',
            'parent_id' => $category->id,
        ]);

    $response->assertSessionHasErrors('parent_id');
});

test('prevents circular parent-child reference to descendants', function () {
    $parent = Category::factory()->create([
        'name' => 'Restaurantes',
        'slug' => 'restaurantes',
    ]);

    $child = Category::factory()->create([
        'name' => 'Pizzas',
        'slug' => 'pizzas',
        'parent_id' => $parent->id,
    ]);

    $grandchild = Category::factory()->create([
        'name' => 'Pizzas Artesanales',
        'slug' => 'pizzas-artesanales',
        'parent_id' => $child->id,
    ]);

    // Intentar que el abuelo tenga como padre al nieto (circular)
    $response = $this->actingAs($this->admin)
        ->put(route('admin.categories.update', $parent), [
            'name' => 'Restaurantes',
            'slug' => 'restaurantes',
            'parent_id' => $grandchild->id,
        ]);

    $response->assertSessionHasErrors('parent_id');
});

test('super admin can update a category', function () {
    $category = Category::factory()->create([
        'name' => 'Moda',
        'slug' => 'moda',
        'sort_order' => 5,
    ]);

    $response = $this->actingAs($this->admin)
        ->put(route('admin.categories.update', $category), [
            'name' => 'Ropa y Moda',
            'slug' => 'ropa-y-moda',
            'sort_order' => 2,
        ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Ropa y Moda',
        'slug' => 'ropa-y-moda',
        'sort_order' => 2,
    ]);
});

test('super admin can toggle category status', function () {
    $category = Category::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.categories.toggle', $category));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'is_active' => false,
    ]);
});

test('block deleting category when it has children', function () {
    $parent = Category::factory()->create();
    $child = Category::factory()->create([
        'parent_id' => $parent->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.categories.destroy', $parent));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('categories', [
        'id' => $parent->id,
        'deleted_at' => null,
    ]);
});

test('block deleting category when it has businesses associated', function () {
    $category = Category::factory()->create();
    $business = Business::factory()->create();
    $category->businesses()->attach($business->id);

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.categories.destroy', $category));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'deleted_at' => null,
    ]);
});

test('block deleting category when it has products associated', function () {
    $category = Category::factory()->create();
    
    // Asignar un producto ficticio en la tabla pivote de categorías de productos
    $productId = fake()->uuid();
    
    \Illuminate\Support\Facades\DB::table('product_categories')->insert([
        'id' => fake()->uuid(),
        'product_id' => $productId,
        'category_id' => $category->id,
        'sort_order' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.categories.destroy', $category));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'deleted_at' => null,
    ]);
});

test('allow deleting category when it has no associations', function () {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->admin)
        ->delete(route('admin.categories.destroy', $category));

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    // Verificar soft-delete
    $this->assertSoftDeleted('categories', [
        'id' => $category->id,
    ]);
});
