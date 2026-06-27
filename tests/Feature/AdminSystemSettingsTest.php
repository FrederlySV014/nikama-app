<?php

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\SystemSetting;
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

test('guests and non-admins cannot access system settings and audit log routes', function () {
    $this->get(route('admin.system.settings.edit'))->assertRedirect(route('login'));
    $this->get(route('admin.system.audit-logs'))->assertRedirect(route('login'));

    $this->actingAs($this->customerUser);
    $this->get(route('admin.system.settings.edit'))->assertForbidden();
    $this->get(route('admin.system.audit-logs'))->assertForbidden();
});

test('super admin can load system settings page with default values', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.system.settings.edit'));

    $response->assertSuccessful()
        ->assertSee('min_driver_payout')
        ->assertSee('20.00')
        ->assertSee('min_business_payout')
        ->assertSee('50.00')
        ->assertSee('soporte@nikama.pe');
});

test('super admin can update system settings', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.system.settings.update'), [
            'settings' => [
                'min_driver_payout' => '35.00',
                'min_business_payout' => '75.00',
                'support_email' => 'ayuda@nikama.pe',
                'support_phone' => '+51 987 654 321',
                'general_commission_percentage' => '12.50',
                'delivery_base_fee' => '6.00',
            ],
        ]);

    $response->assertRedirect();

    $this->assertEquals('35.00', SystemSetting::where('key', 'min_driver_payout')->first()->value);
    $this->assertEquals('75.00', SystemSetting::where('key', 'min_business_payout')->first()->value);
    $this->assertEquals('ayuda@nikama.pe', SystemSetting::where('key', 'support_email')->first()->value);
    $this->assertEquals('+51 987 654 321', SystemSetting::where('key', 'support_phone')->first()->value);
    $this->assertEquals('12.50', SystemSetting::where('key', 'general_commission_percentage')->first()->value);
    $this->assertEquals('6.00', SystemSetting::where('key', 'delivery_base_fee')->first()->value);
});

test('super admin can view audit logs', function () {
    $log = ActivityLog::create([
        'user_id' => $this->adminUser->id,
        'action' => 'update_settings',
        'entity_type' => SystemSetting::class,
        'entity_id' => 'some-uuid',
        'old_values' => ['min_driver_payout' => '20.00'],
        'new_values' => ['min_driver_payout' => '35.00'],
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Mozilla/5.0 Testing',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.system.audit-logs'));

    $response->assertSuccessful()
        ->assertSee('update_settings')
        ->assertSee('SystemSetting')
        ->assertSee('127.0.0.1')
        ->assertSee('Mozilla/5.0 Testing')
        ->assertSee($this->adminUser->email);
});
