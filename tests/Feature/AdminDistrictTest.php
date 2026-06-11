<?php

use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Create super admin
    $this->admin = User::factory()->create();
    $adminRole = Role::where('slug', Role::SUPER_ADMIN)->first();
    $this->admin->roles()->attach($adminRole->id);

    // Create client user
    $this->customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $this->customer->roles()->attach($customerRole->id);
});

test('guests are redirected from districts settings page', function () {
    $this->get(route('admin.settings.districts.edit'))
        ->assertRedirect(route('login'));
});

test('customers are forbidden from accessing districts settings', function () {
    $this->actingAs($this->customer)
        ->get(route('admin.settings.districts.edit'))
        ->assertStatus(403);
});

test('super admin can view districts settings page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.settings.districts.edit'))
        ->assertStatus(200)
        ->assertSee('Zonas de Cobertura de Nikama')
        ->assertSee('Chiclayo');
});

test('super admin can update active coverage districts', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.settings.districts.update'), [
            'districts' => ['Lambayeque|Chiclayo|Chiclayo', 'Lambayeque|Chiclayo|Pimentel', 'Lambayeque|Chiclayo|La Victoria'],
        ])
        ->assertRedirect(route('admin.settings.districts.edit'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('system_settings', [
        'key' => 'active_districts',
    ]);

    $active = SystemSetting::getActiveDistricts();
    expect($active)->toEqualCanonicalizing(['Lambayeque|Chiclayo|Chiclayo', 'Lambayeque|Chiclayo|Pimentel', 'Lambayeque|Chiclayo|La Victoria']);
});

test('updating coverage districts validates invalid districts', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.settings.districts.edit'))
        ->post(route('admin.settings.districts.update'), [
            'districts' => ['Lambayeque|Chiclayo|Chiclayo', 'InvalidDistrictName'],
        ])
        ->assertRedirect(route('admin.settings.districts.edit'))
        ->assertSessionHasErrors('districts.1');
});
