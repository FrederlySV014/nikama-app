<?php

use App\Models\Business;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('welcome page displays active root categories from database', function () {
    // 1. Crear categoría activa raíz
    $activeRoot = Category::factory()->create([
        'name' => 'Restaurantes',
        'slug' => 'restaurantes',
        'is_active' => true,
        'parent_id' => null,
    ]);

    // 2. Crear categoría inactiva raíz (no debe mostrarse)
    $inactiveRoot = Category::factory()->create([
        'name' => 'Ferreterías',
        'slug' => 'ferreterias',
        'is_active' => false,
        'parent_id' => null,
    ]);

    // 3. Crear categoría activa hija (no debe mostrarse)
    $activeChild = Category::factory()->create([
        'name' => 'Hamburguesas',
        'slug' => 'hamburguesas',
        'is_active' => true,
        'parent_id' => $activeRoot->id,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    
    // Debe ver el nombre de la categoría activa raíz
    $response->assertSee('Restaurantes');
    
    // NO debe ver el nombre de la categoría inactiva raíz
    $response->assertDontSee('Ferreterías');
    
    // NO debe ver el nombre de la categoría hija
    $response->assertDontSee('Hamburguesas');
});

test('welcome page displays approved and active businesses from database', function () {
    // 1. Crear categoría para asociar a los negocios
    $category = Category::factory()->create([
        'name' => 'Supermercado',
        'is_active' => true,
    ]);

    // 2. Crear negocio aprobado y activo
    $approvedActive = Business::factory()->create([
        'business_name' => 'Metro Plaza',
        'slug' => 'metro-plaza',
        'status' => Business::STATUS_APPROVED,
        'is_active' => true,
    ]);
    $approvedActive->categories()->attach($category->id);

    // 3. Crear negocio pendiente (no debe mostrarse)
    $pending = Business::factory()->create([
        'business_name' => 'Bodega Don Pepe',
        'slug' => 'bodega-don-pepe',
        'status' => Business::STATUS_PENDING,
        'is_active' => true,
    ]);
    $pending->categories()->attach($category->id);

    // 4. Crear negocio rechazado (no debe mostrarse)
    $rejected = Business::factory()->create([
        'business_name' => 'KFC Chiclayo',
        'slug' => 'kfc-chiclayo',
        'status' => Business::STATUS_REJECTED,
        'is_active' => true,
    ]);
    $rejected->categories()->attach($category->id);

    // 5. Crear negocio aprobado pero inactivo (no debe mostrarse)
    $approvedInactive = Business::factory()->create([
        'business_name' => 'Tottus Center',
        'slug' => 'tottus-center',
        'status' => Business::STATUS_APPROVED,
        'is_active' => false,
    ]);
    $approvedInactive->categories()->attach($category->id);

    $response = $this->get('/');

    $response->assertStatus(200);

    // Debe ver el negocio aprobado y activo
    $response->assertSee('Metro Plaza');

    // NO debe ver los otros negocios
    $response->assertDontSee('Bodega Don Pepe');
    $response->assertDontSee('KFC Chiclayo');
    $response->assertDontSee('Tottus Center');
});
