<?php

use App\Models\Business;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->superAdminRole = Role::where('slug', Role::SUPER_ADMIN)->first();
    $this->customerRole = Role::where('slug', Role::CUSTOMER)->first();

    // Create a Super Admin
    $this->adminUser = User::factory()->create();
    $this->adminUser->roles()->attach($this->superAdminRole->id);

    // Create a regular customer
    $this->customerUser = User::factory()->create();
    $this->customerUser->roles()->attach($this->customerRole->id);
});

test('guests cannot access business management routes', function () {
    $this->get(route('admin.businesses.index'))->assertRedirect(route('login'));

    $business = Business::factory()->create();
    $this->get(route('admin.businesses.show', $business))->assertRedirect(route('login'));
    $this->post(route('admin.businesses.toggle-active', $business))->assertRedirect(route('login'));
    $this->post(route('admin.businesses.toggle-featured', $business))->assertRedirect(route('login'));
    $this->post(route('admin.businesses.toggle-accepts-orders', $business))->assertRedirect(route('login'));
    $this->post(route('admin.businesses.toggle-suspension', $business))->assertRedirect(route('login'));
});

test('non-admins cannot access business management routes', function () {
    $this->actingAs($this->customerUser);

    $this->get(route('admin.businesses.index'))->assertForbidden();

    $business = Business::factory()->create();
    $this->get(route('admin.businesses.show', $business))->assertForbidden();
    $this->post(route('admin.businesses.toggle-active', $business))->assertForbidden();
    $this->post(route('admin.businesses.toggle-featured', $business))->assertForbidden();
    $this->post(route('admin.businesses.toggle-accepts-orders', $business))->assertForbidden();
    $this->post(route('admin.businesses.toggle-suspension', $business))->assertForbidden();
});

test('super admins can load businesses index with statistics', function () {
    Business::factory()->create(['status' => Business::STATUS_APPROVED]);
    Business::factory()->create(['status' => Business::STATUS_PENDING]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.businesses.index'));

    $response->assertSuccessful()
        ->assertViewIs('admin.businesses.index')
        ->assertSee('Gestión de Negocios')
        ->assertSee('Aprobados')
        ->assertSee('Pendientes');
});

test('businesses can be filtered by status tab', function () {
    $approved = Business::factory()->create([
        'business_name' => 'Comercio Aprobado',
        'status' => Business::STATUS_APPROVED,
    ]);
    $pending = Business::factory()->create([
        'business_name' => 'Comercio Pendiente',
        'status' => Business::STATUS_PENDING,
    ]);

    // Filter by approved
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.businesses.index', ['tab' => 'approved']));

    $response->assertSuccessful()
        ->assertSee('Comercio Aprobado')
        ->assertDontSee('Comercio Pendiente');

    // Filter by pending
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.businesses.index', ['tab' => 'pending']));

    $response->assertSuccessful()
        ->assertSee('Comercio Pendiente')
        ->assertDontSee('Comercio Aprobado');
});

test('businesses can be filtered by active status', function () {
    $active = Business::factory()->create([
        'business_name' => 'Comercio Activo',
        'is_active' => true,
    ]);
    $inactive = Business::factory()->create([
        'business_name' => 'Comercio Inactivo',
        'is_active' => false,
    ]);

    // Filter active
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.businesses.index', ['active' => 'active']));

    $response->assertSuccessful()
        ->assertSee('Comercio Activo')
        ->assertDontSee('Comercio Inactivo');

    // Filter inactive
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.businesses.index', ['active' => 'inactive']));

    $response->assertSuccessful()
        ->assertSee('Comercio Inactivo')
        ->assertDontSee('Comercio Activo');
});

test('businesses can be filtered by featured status', function () {
    $featured = Business::factory()->create([
        'business_name' => 'Comercio Destacado',
        'is_featured' => true,
    ]);
    $standard = Business::factory()->create([
        'business_name' => 'Comercio Estandar',
        'is_featured' => false,
    ]);

    // Filter featured
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.businesses.index', ['featured' => 'featured']));

    $response->assertSuccessful()
        ->assertSee('Comercio Destacado')
        ->assertDontSee('Comercio Estandar');

    // Filter standard
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.businesses.index', ['featured' => 'standard']));

    $response->assertSuccessful()
        ->assertSee('Comercio Estandar')
        ->assertDontSee('Comercio Destacado');
});

test('businesses can be searched by text', function () {
    $business1 = Business::factory()->create(['business_name' => 'Restaurante Luffy']);
    $business2 = Business::factory()->create(['business_name' => 'Bodega Zoro']);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.businesses.index', ['search' => 'Luffy']));

    $response->assertSuccessful()
        ->assertSee('Restaurante Luffy')
        ->assertDontSee('Bodega Zoro');
});

test('super admin can load business details', function () {
    $business = Business::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.businesses.show', $business));

    $response->assertSuccessful()
        ->assertViewIs('admin.businesses.show')
        ->assertSee($business->business_name)
        ->assertSee($business->slug);
});

test('super admin can toggle business active status', function () {
    $business = Business::factory()->create(['is_active' => true]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.businesses.toggle-active', $business));

    $response->assertRedirect();
    $this->assertFalse($business->fresh()->is_active);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.businesses.toggle-active', $business));

    $response->assertRedirect();
    $this->assertTrue($business->fresh()->is_active);
});

test('super admin can toggle business featured status', function () {
    $business = Business::factory()->create(['is_featured' => false]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.businesses.toggle-featured', $business));

    $response->assertRedirect();
    $this->assertTrue($business->fresh()->is_featured);
});

test('super admin can toggle business accepts orders status', function () {
    $business = Business::factory()->create(['accepts_orders' => true]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.businesses.toggle-accepts-orders', $business));

    $response->assertRedirect();
    $this->assertFalse($business->fresh()->accepts_orders);
});

test('super admin can toggle suspension on approved business', function () {
    $business = Business::factory()->create([
        'status' => Business::STATUS_APPROVED,
        'accepts_orders' => true,
    ]);

    // Suspend business
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.businesses.toggle-suspension', $business));

    $response->assertRedirect();
    $this->assertEquals(Business::STATUS_SUSPENDED, $business->fresh()->status);
    $this->assertNotNull($business->fresh()->suspended_at);
    $this->assertFalse($business->fresh()->accepts_orders);

    // Reactivate / unsuspend business
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.businesses.toggle-suspension', $business));

    $response->assertRedirect();
    $this->assertEquals(Business::STATUS_APPROVED, $business->fresh()->status);
    $this->assertNull($business->fresh()->suspended_at);
});
