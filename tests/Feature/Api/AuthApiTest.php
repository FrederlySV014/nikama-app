<?php

use App\Models\DriverProfile;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('api login validates credentials and returns sanctum token', function () {
    $customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $customer->roles()->attach($customerRole->id);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $customer->email,
        'password' => 'password',
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'message',
        'token',
        'user' => [
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'roles',
        ],
    ]);

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $customer->id,
    ]);
});

test('api login fails with incorrect password', function () {
    $customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $customer->roles()->attach($customerRole->id);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $customer->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);
    $response->assertJson([
        'success' => false,
        'message' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
    ]);
});

test('api register customer creates user, profile, and returns token', function () {
    $response = $this->postJson('/api/v1/auth/register/customer', [
        'first_name' => 'Luffy',
        'last_name' => 'Monkey',
        'email' => 'luffy@onepiece.com',
        'password' => 'gomugomuno',
        'password_confirmation' => 'gomugomuno',
        'phone' => '987654321',
        'dni' => '12345678',
        'birth_date' => '2000-05-05',
        'gender' => 'male',
    ]);

    $response->assertStatus(210); // Check AuthController: we set 210 for registerCustomer
    $response->assertJsonStructure([
        'success',
        'message',
        'token',
        'user',
    ]);

    $this->assertDatabaseHas('users', ['email' => 'luffy@onepiece.com']);
    $user = User::where('email', 'luffy@onepiece.com')->first();
    $this->assertTrue($user->hasRole(Role::CUSTOMER));
    $this->assertDatabaseHas('customer_profiles', ['user_id' => $user->id]);
});

test('api register driver creates user, profile with pending status, and returns token', function () {
    $response = $this->postJson('/api/v1/auth/register/driver', [
        'first_name' => 'Zoro',
        'last_name' => 'Roronoa',
        'email' => 'zoro@onepiece.com',
        'password' => 'santoryu',
        'password_confirmation' => 'santoryu',
        'phone' => '912345678',
        'vehicle_type' => 'motorcycle',
        'license_number' => 'ABC-12345',
        'vehicle_brand' => 'Honda',
        'vehicle_model' => 'CB190R',
        'vehicle_color' => 'Green',
        'license_plate' => 'PLATE-Z',
        'emergency_contact_name' => 'Kuina',
        'emergency_contact_phone' => '999888777',
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'success',
        'message',
        'token',
        'user',
    ]);

    $this->assertDatabaseHas('users', ['email' => 'zoro@onepiece.com']);
    $user = User::where('email', 'zoro@onepiece.com')->first();
    $this->assertTrue($user->hasRole(Role::DRIVER));
    $this->assertDatabaseHas('driver_profiles', [
        'user_id' => $user->id,
        'status' => DriverProfile::STATUS_PENDING,
    ]);
});

test('api profile retrieves auth user details', function () {
    $customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $customer->roles()->attach($customerRole->id);

    $response = $this->actingAs($customer, 'sanctum')->getJson('/api/v1/auth/profile');

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'user' => [
            'email' => $customer->email,
        ],
    ]);
});

test('api logout revokes token', function () {
    $customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $customer->roles()->attach($customerRole->id);

    // Generate token
    $token = $customer->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/v1/auth/logout');

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Cierre de sesión exitoso.',
    ]);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $customer->id,
    ]);
});
