<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->superAdminRole = Role::where('slug', Role::SUPER_ADMIN)->first();
    $this->customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $this->sellerRole = Role::where('slug', Role::SELLER)->first();
    $this->driverRole = Role::where('slug', Role::DRIVER)->first();

    // Create a Super Admin
    $this->adminUser = User::factory()->create();
    $this->adminUser->roles()->attach($this->superAdminRole->id);

    // Create a regular customer
    $this->customerUser = User::factory()->create();
    $this->customerUser->roles()->attach($this->customerRole->id);
});

test('guests cannot access user management routes', function () {
    $this->get(route('admin.users.index'))->assertRedirect(route('login'));

    $otherUser = User::factory()->create();
    $this->post(route('admin.users.toggle-status', $otherUser))->assertRedirect(route('login'));
});

test('non-admins cannot access user management routes', function () {
    $this->actingAs($this->customerUser);

    $this->get(route('admin.users.index'))->assertForbidden();

    $otherUser = User::factory()->create();
    $this->post(route('admin.users.toggle-status', $otherUser))->assertForbidden();
});

test('super admins can load users index with counts', function () {
    // Create some users with different roles
    $seller = User::factory()->create();
    $seller->roles()->attach($this->sellerRole->id);

    $driver = User::factory()->create();
    $driver->roles()->attach($this->driverRole->id);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.users.index'));

    $response->assertSuccessful()
        ->assertViewIs('admin.users.index')
        ->assertSee('Gestión de Usuarios')
        // Ensure counts are calculated
        ->assertSee('1'); // seller & driver count
});

test('users can be filtered by role tab', function () {
    // Create a customer user
    $customer = User::factory()->create(['first_name' => 'LuffyCustomer']);
    $customer->roles()->attach($this->customerRole->id);

    // Create a seller user
    $seller = User::factory()->create(['first_name' => 'ZoroSeller']);
    $seller->roles()->attach($this->sellerRole->id);

    // Filter by customer tab
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.users.index', ['tab' => 'customer']));

    $response->assertSuccessful()
        ->assertSee('LuffyCustomer')
        ->assertDontSee('ZoroSeller');

    // Filter by seller tab
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.users.index', ['tab' => 'seller']));

    $response->assertSuccessful()
        ->assertSee('ZoroSeller')
        ->assertDontSee('LuffyCustomer');
});

test('users can be filtered by active status', function () {
    $activeUser = User::factory()->create(['first_name' => 'ActiveLuffy', 'is_active' => true]);
    $activeUser->roles()->attach($this->customerRole->id);

    $blockedUser = User::factory()->create(['first_name' => 'BlockedLuffy', 'is_active' => false]);
    $blockedUser->roles()->attach($this->customerRole->id);

    // Filter active
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.users.index', ['status' => 'active']));

    $response->assertSuccessful()
        ->assertSee('ActiveLuffy')
        ->assertDontSee('BlockedLuffy');

    // Filter blocked
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.users.index', ['status' => 'blocked']));

    $response->assertSuccessful()
        ->assertSee('BlockedLuffy')
        ->assertDontSee('ActiveLuffy');
});

test('users can be searched by name', function () {
    $user1 = User::factory()->create(['first_name' => 'UniqueNameLuffy']);
    $user1->roles()->attach($this->customerRole->id);

    $user2 = User::factory()->create(['first_name' => 'CommonNameZoro']);
    $user2->roles()->attach($this->customerRole->id);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.users.index', ['search' => 'UniqueName']));

    $response->assertSuccessful()
        ->assertSee('UniqueNameLuffy')
        ->assertDontSee('CommonNameZoro');
});

test('super admin can toggle user active status', function () {
    $user = User::factory()->create(['is_active' => true]);
    $user->roles()->attach($this->customerRole->id);

    // Block the user
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.users.toggle-status', $user));

    $response->assertRedirect();
    $this->assertFalse($user->fresh()->is_active);
    $this->assertNotNull($user->fresh()->blocked_at);

    // Activate the user
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.users.toggle-status', $user));

    $response->assertRedirect();
    $this->assertTrue($user->fresh()->is_active);
    $this->assertNull($user->fresh()->blocked_at);
});

test('super admin cannot block their own account', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.users.toggle-status', $this->adminUser));

    $response->assertRedirect();
    $this->assertTrue($this->adminUser->fresh()->is_active);
    $this->assertNull($this->adminUser->fresh()->blocked_at);
});
