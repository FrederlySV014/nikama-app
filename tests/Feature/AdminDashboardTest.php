<?php

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\DriverProfile;
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

test('guests are redirected to login', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    $this->get(route('admin.dashboard.pending-applications'))->assertRedirect(route('login'));
});

test('non-admins cannot access admin dashboard or endpoints', function () {
    $this->actingAs($this->customerUser);

    $this->get(route('admin.dashboard'))->assertForbidden();
    $this->get(route('admin.dashboard.pending-applications'))->assertForbidden();
});

test('super admins can access their dashboard with stats', function () {
    // Create some test data
    Business::factory()->create(['status' => Business::STATUS_PENDING]);
    Business::factory()->create(['status' => Business::STATUS_APPROVED]);

    $driverUser = User::factory()->create();
    $driverUser->roles()->attach($this->driverRole->id);
    DriverProfile::factory()->create([
        'user_id' => $driverUser->id,
        'status' => DriverProfile::STATUS_PENDING,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.dashboard'));

    $response->assertSuccessful()
        ->assertViewIs('admin.dashboard')
        ->assertSee('Panel de Control General')
        ->assertSee('1'); // counts of pending seller/driver should be visible
});

test('super admins can retrieve pending applications list via JSON', function () {
    // Create pending business
    $sellerUser = User::factory()->create(['first_name' => 'Luffy', 'last_name' => 'Monkey']);
    $sellerUser->roles()->attach($this->sellerRole->id);
    $business = Business::factory()->create([
        'business_name' => 'Restaurante Luffy',
        'status' => Business::STATUS_PENDING,
    ]);

    BusinessUser::create([
        'business_id' => $business->id,
        'user_id' => $sellerUser->id,
        'role' => BusinessUser::ROLE_ADMIN,
        'is_active' => true,
        'joined_at' => now(),
    ]);

    // Create approved business
    Business::factory()->create([
        'business_name' => 'Restaurante Zoro',
        'status' => Business::STATUS_APPROVED,
    ]);

    // Create pending driver profile
    $driverUser = User::factory()->create(['first_name' => 'Zoro', 'last_name' => 'Roronoa']);
    $driverUser->roles()->attach($this->driverRole->id);
    DriverProfile::factory()->create([
        'user_id' => $driverUser->id,
        'status' => DriverProfile::STATUS_PENDING,
    ]);

    // Create active driver profile
    $activeDriverUser = User::factory()->create(['first_name' => 'Sanji', 'last_name' => 'Vinsmoke']);
    $activeDriverUser->roles()->attach($this->driverRole->id);
    DriverProfile::factory()->create([
        'user_id' => $activeDriverUser->id,
        'status' => DriverProfile::STATUS_ACTIVE,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->getJson(route('admin.dashboard.pending-applications'));

    $response->assertSuccessful()
        ->assertJsonStructure([
            'sellers' => [
                '*' => ['id', 'business_name', 'owner_name'],
            ],
            'drivers' => [
                '*' => ['id', 'name'],
            ],
            'pending_sellers_count',
            'pending_drivers_count',
            'active_sellers_count',
            'active_drivers_count',
        ])
        ->assertJsonFragment([
            'business_name' => 'Restaurante Luffy',
            'owner_name' => 'Luffy Monkey',
        ])
        ->assertJsonFragment([
            'name' => 'Zoro Roronoa',
        ])
        ->assertJson([
            'pending_sellers_count' => 1,
            'pending_drivers_count' => 1,
            'active_sellers_count' => 1,
            'active_drivers_count' => 1,
        ]);
});
