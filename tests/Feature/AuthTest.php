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
});

test('customer login only allows customer role', function () {
    // 1. Create a customer user
    $customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $customer->roles()->attach($customerRole->id);

    // Try logging in as customer
    $response = $this->post('/auth/login', [
        'email' => $customer->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('public.welcome'));
    $this->assertAuthenticatedAs($customer);

    // Logout
    $this->post('/logout');
    $this->assertGuest();

    // 2. Create a seller user
    $seller = User::factory()->create();
    $sellerRole = Role::where('slug', Role::SELLER)->first();
    $seller->roles()->attach($sellerRole->id);

    // Try logging in as seller in customer portal
    $response = $this->post('/auth/login', [
        'email' => $seller->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('seller login only allows seller role and handles approval', function () {
    // 1. Create a seller user with approved business
    $seller = User::factory()->create();
    $sellerRole = Role::where('slug', Role::SELLER)->first();
    $seller->roles()->attach($sellerRole->id);

    $business = Business::factory()->create(['status' => Business::STATUS_APPROVED]);
    BusinessUser::create([
        'business_id' => $business->id,
        'user_id' => $seller->id,
        'role' => BusinessUser::ROLE_ADMIN,
        'is_active' => true,
        'joined_at' => now(),
    ]);

    // Login approved seller
    $response = $this->post('/auth/seller-login', [
        'email' => $seller->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('seller.dashboard'));
    $this->assertAuthenticatedAs($seller);

    // Logout
    $this->post('/logout');

    // 2. Try logging in as customer in seller portal
    $customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $customer->roles()->attach($customerRole->id);

    $response = $this->post('/auth/seller-login', [
        'email' => $customer->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();

    // 3. Create a seller with pending business
    $pendingSeller = User::factory()->create();
    $pendingSeller->roles()->attach($sellerRole->id);

    $pendingBusiness = Business::factory()->create(['status' => Business::STATUS_PENDING]);
    BusinessUser::create([
        'business_id' => $pendingBusiness->id,
        'user_id' => $pendingSeller->id,
        'role' => BusinessUser::ROLE_ADMIN,
        'is_active' => true,
        'joined_at' => now(),
    ]);

    $response = $this->post('/auth/seller-login', [
        'email' => $pendingSeller->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('auth.pending-review'));
    $this->assertAuthenticatedAs($pendingSeller);
});

test('driver login only allows driver role and handles approval', function () {
    // 1. Create a driver user with active profile
    $driver = User::factory()->create();
    $driverRole = Role::where('slug', Role::DRIVER)->first();
    $driver->roles()->attach($driverRole->id);

    DriverProfile::factory()->create([
        'user_id' => $driver->id,
        'status' => DriverProfile::STATUS_ACTIVE,
    ]);

    // Login active driver
    $response = $this->post('/auth/driver-login', [
        'email' => $driver->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('driver.dashboard'));
    $this->assertAuthenticatedAs($driver);

    // Logout
    $this->post('/logout');

    // 2. Try logging in as customer in driver portal
    $customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $customer->roles()->attach($customerRole->id);

    $response = $this->post('/auth/driver-login', [
        'email' => $customer->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();

    // 3. Create a driver with pending profile
    $pendingDriver = User::factory()->create();
    $pendingDriver->roles()->attach($driverRole->id);

    DriverProfile::factory()->create([
        'user_id' => $pendingDriver->id,
        'status' => DriverProfile::STATUS_PENDING,
    ]);

    $response = $this->post('/auth/driver-login', [
        'email' => $pendingDriver->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('auth.pending-review'));
    $this->assertAuthenticatedAs($pendingDriver);
});

test('admin login only allows super_admin role', function () {
    // 1. Create a super_admin user
    $admin = User::factory()->create();
    $adminRole = Role::where('slug', Role::SUPER_ADMIN)->first();
    $admin->roles()->attach($adminRole->id);

    // Try logging in as admin
    $response = $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);

    // Logout
    $this->post('/logout');
    $this->assertGuest();

    // 2. Create a customer user
    $customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $customer->roles()->attach($customerRole->id);

    // Try logging in as customer in admin portal
    $response = $this->post('/admin/login', [
        'email' => $customer->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});
