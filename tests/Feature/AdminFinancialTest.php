<?php

use App\Models\Business;
use App\Models\BusinessCommission;
use App\Models\BusinessPayout;
use App\Models\Role;
use App\Models\User;
use App\Models\WalletTransaction;
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

test('guests and non-admins cannot access financial routes', function () {
    $this->get(route('admin.financial.payouts'))->assertRedirect(route('login'));
    $this->get(route('admin.financial.commissions'))->assertRedirect(route('login'));
    $this->get(route('admin.financial.transactions'))->assertRedirect(route('login'));

    $this->actingAs($this->customerUser);
    $this->get(route('admin.financial.payouts'))->assertForbidden();
    $this->get(route('admin.financial.commissions'))->assertForbidden();
    $this->get(route('admin.financial.transactions'))->assertForbidden();
});

test('super admin can load payouts list', function () {
    $business = Business::factory()->create();
    BusinessPayout::create([
        'business_id' => $business->id,
        'amount' => 100.00,
        'commission_deducted' => 10.00,
        'net_amount' => 90.00,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.financial.payouts'));

    $response->assertSuccessful()
        ->assertSee($business->business_name)
        ->assertSee('S/ 90.00');
});

test('super admin can process business payout and debit wallet', function () {
    $business = Business::factory()->create();
    $payout = BusinessPayout::create([
        'business_id' => $business->id,
        'amount' => 100.00,
        'commission_deducted' => 10.00,
        'net_amount' => 90.00,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.financial.payouts.process', ['type' => 'business', 'id' => $payout->id]), [
            'status' => 'processed',
            'transaction_reference' => 'TX-123456',
            'notes' => 'Pago realizado por transferencia bancaria',
        ]);

    $response->assertRedirect();
    $this->assertEquals('processed', $payout->fresh()->status);
    $this->assertEquals('TX-123456', $payout->fresh()->transaction_reference);

    // Verify wallet debit transaction was created
    $transaction = WalletTransaction::where('holder_type', Business::class)
        ->where('holder_id', $business->id)
        ->first();

    $this->assertNotNull($transaction);
    $this->assertEquals(90.00, $transaction->amount);
    $this->assertEquals(WalletTransaction::TYPE_DEBIT, $transaction->type);
});

test('super admin can reject payout', function () {
    $business = Business::factory()->create();
    $payout = BusinessPayout::create([
        'business_id' => $business->id,
        'amount' => 100.00,
        'commission_deducted' => 10.00,
        'net_amount' => 90.00,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.financial.payouts.process', ['type' => 'business', 'id' => $payout->id]), [
            'status' => 'failed',
            'notes' => 'Datos bancarios incorrectos',
        ]);

    $response->assertRedirect();
    $this->assertEquals('failed', $payout->fresh()->status);

    // Ensure no debit transaction was registered
    $transaction = WalletTransaction::where('holder_type', Business::class)
        ->where('holder_id', $business->id)
        ->first();

    $this->assertNull($transaction);
});

test('super admin can load commissions list', function () {
    $business = Business::factory()->create(['status' => Business::STATUS_APPROVED]);
    BusinessCommission::create([
        'business_id' => $business->id,
        'commission_type' => 'percentage',
        'commission_value' => 15.00,
        'is_active' => true,
        'starts_at' => now(),
    ]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.financial.commissions'));

    $response->assertSuccessful()
        ->assertSee($business->business_name)
        ->assertSee('15%');
});

test('super admin can store new commission rule', function () {
    $business = Business::factory()->create(['status' => Business::STATUS_APPROVED]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.financial.commissions.store'), [
            'business_id' => $business->id,
            'commission_type' => 'fixed',
            'commission_value' => 5.00,
            'starts_at' => now()->format('Y-m-d\TH:i'),
        ]);

    $response->assertRedirect();

    $commission = BusinessCommission::where('business_id', $business->id)->first();
    $this->assertNotNull($commission);
    $this->assertEquals('fixed', $commission->commission_type);
    $this->assertEquals(5.00, $commission->commission_value);
    $this->assertTrue($commission->is_active);
});

test('super admin can toggle commission active status', function () {
    $business = Business::factory()->create(['status' => Business::STATUS_APPROVED]);
    $commission = BusinessCommission::create([
        'business_id' => $business->id,
        'commission_type' => 'percentage',
        'commission_value' => 15.00,
        'is_active' => true,
        'starts_at' => now(),
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.financial.commissions.toggle', $commission));

    $response->assertRedirect();
    $this->assertFalse($commission->fresh()->is_active);
});

test('super admin can view wallet transactions audit log', function () {
    $business = Business::factory()->create();
    $transaction = WalletTransaction::create([
        'holder_type' => Business::class,
        'holder_id' => $business->id,
        'amount' => 120.00,
        'type' => 'credit',
        'transaction_type' => 'order_payment',
        'description' => 'Pago por pedido #1002',
    ]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.financial.transactions'));

    $response->assertSuccessful()
        ->assertSee($business->id)
        ->assertSee('+ S/ 120.00')
        ->assertSee('Pago por pedido #1002');
});
