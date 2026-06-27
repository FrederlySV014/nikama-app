<?php

use App\Models\Discount;
use App\Models\PromotionalBanner;
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

test('guests and non-admins cannot access marketing routes', function () {
    $this->get(route('admin.marketing.banners'))->assertRedirect(route('login'));
    $this->get(route('admin.marketing.discounts'))->assertRedirect(route('login'));

    $this->actingAs($this->customerUser);
    $this->get(route('admin.marketing.banners'))->assertForbidden();
    $this->get(route('admin.marketing.discounts'))->assertForbidden();
});

test('super admin can load banners list', function () {
    $banner = PromotionalBanner::create([
        'title' => 'Descuento Increíble',
        'image_url' => 'https://example.com/banner.jpg',
        'action_type' => 'external_link',
        'action_url' => 'https://example.com/promo',
        'sort_order' => 1,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.marketing.banners'));

    $response->assertSuccessful()
        ->assertSee('Descuento Increíble')
        ->assertSee('https://example.com/promo');
});

test('super admin can store new banner', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.marketing.banners.store'), [
            'title' => 'Nuevo Banner Publicitario',
            'image_url' => 'https://example.com/new-banner.jpg',
            'action_type' => 'external_link',
            'action_url' => 'https://example.com/new-promo',
            'sort_order' => 5,
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'expires_at' => now()->addDays(5)->format('Y-m-d\TH:i'),
        ]);

    $response->assertRedirect();

    $banner = PromotionalBanner::where('title', 'Nuevo Banner Publicitario')->first();
    $this->assertNotNull($banner);
    $this->assertEquals('https://example.com/new-banner.jpg', $banner->image_url);
    $this->assertEquals('https://example.com/new-promo', $banner->action_url);
    $this->assertEquals(5, $banner->sort_order);
    $this->assertTrue($banner->is_active);
});

test('super admin can toggle banner active status', function () {
    $banner = PromotionalBanner::create([
        'title' => 'Banner Temporal',
        'image_url' => 'https://example.com/banner.jpg',
        'action_type' => 'external_link',
        'action_url' => 'https://example.com/promo',
        'sort_order' => 1,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.marketing.banners.toggle', $banner));

    $response->assertRedirect();
    $this->assertFalse($banner->fresh()->is_active);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.marketing.banners.toggle', $banner));

    $response->assertRedirect();
    $this->assertTrue($banner->fresh()->is_active);
});

test('super admin can load discounts list', function () {
    $discount = Discount::create([
        'created_by_user_id' => $this->adminUser->id,
        'code' => 'SUPERPROMO',
        'name' => 'Super Promo',
        'description' => 'Descuento de prueba',
        'type' => Discount::TYPE_COUPON,
        'discount_type' => 'percentage',
        'discount_value' => 25.00,
        'applies_to' => Discount::APPLIES_TO_ORDER,
        'minimum_order_amount' => 50.00,
        'maximum_discount_amount' => 100.00,
        'usage_limit' => 100,
        'usage_limit_per_user' => 2,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.marketing.discounts'));

    $response->assertSuccessful()
        ->assertSee('SUPERPROMO')
        ->assertSee('Super Promo')
        ->assertSee('25%');
});

test('super admin can store new discount coupon', function () {
    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.marketing.discounts.store'), [
            'code' => 'NUEVO50',
            'name' => 'Descuento 50 Soles',
            'description' => 'Ahorra 50 soles',
            'discount_type' => 'fixed',
            'discount_value' => 50.00,
            'minimum_order_amount' => 150.00,
            'usage_limit' => 200,
            'usage_limit_per_user' => 1,
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'expires_at' => now()->addDays(10)->format('Y-m-d\TH:i'),
        ]);

    $response->assertRedirect();

    $discount = Discount::where('code', 'NUEVO50')->first();
    $this->assertNotNull($discount);
    $this->assertEquals('Descuento 50 Soles', $discount->name);
    $this->assertEquals('fixed', $discount->discount_type);
    $this->assertEquals(50.00, $discount->discount_value);
    $this->assertEquals(150.00, $discount->minimum_order_amount);
    $this->assertTrue($discount->is_active);
});

test('super admin can toggle discount status', function () {
    $discount = Discount::create([
        'created_by_user_id' => $this->adminUser->id,
        'code' => 'TOGGLEOFF',
        'name' => 'Cupón Temporal',
        'type' => Discount::TYPE_COUPON,
        'discount_type' => 'free_delivery',
        'discount_value' => 0,
        'applies_to' => Discount::APPLIES_TO_ORDER,
        'minimum_order_amount' => 30.00,
        'usage_limit' => 10,
        'usage_limit_per_user' => 1,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.marketing.discounts.toggle', $discount));

    $response->assertRedirect();
    $this->assertFalse($discount->fresh()->is_active);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.marketing.discounts.toggle', $discount));

    $response->assertRedirect();
    $this->assertTrue($discount->fresh()->is_active);
});

test('super admin can update banner', function () {
    $banner = PromotionalBanner::create([
        'title' => 'Banner Original',
        'image_url' => 'https://example.com/original.jpg',
        'action_type' => 'external_link',
        'action_url' => 'https://example.com/old',
        'sort_order' => 1,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->put(route('admin.marketing.banners.update', $banner), [
            'title' => 'Banner Modificado',
            'image_url' => 'https://example.com/updated.jpg',
            'action_type' => 'external_link',
            'action_url' => 'https://example.com/new',
            'sort_order' => 10,
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'expires_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ]);

    $response->assertRedirect();
    $this->assertEquals('Banner Modificado', $banner->fresh()->title);
    $this->assertEquals('https://example.com/updated.jpg', $banner->fresh()->image_url);
    $this->assertEquals('https://example.com/new', $banner->fresh()->action_url);
    $this->assertEquals(10, $banner->fresh()->sort_order);
});

test('super admin can delete banner', function () {
    $banner = PromotionalBanner::create([
        'title' => 'Banner para eliminar',
        'image_url' => 'https://example.com/banner.jpg',
        'action_type' => 'external_link',
        'action_url' => 'https://example.com/promo',
        'sort_order' => 1,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->delete(route('admin.marketing.banners.destroy', $banner));

    $response->assertRedirect();
    $this->assertNull(PromotionalBanner::find($banner->id));
});

test('super admin can update discount coupon', function () {
    $discount = Discount::create([
        'created_by_user_id' => $this->adminUser->id,
        'code' => 'PROMO10',
        'name' => 'Descuento 10%',
        'type' => Discount::TYPE_COUPON,
        'discount_type' => 'percentage',
        'discount_value' => 10.00,
        'applies_to' => Discount::APPLIES_TO_ORDER,
        'minimum_order_amount' => 20.00,
        'usage_limit' => 50,
        'usage_limit_per_user' => 1,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->put(route('admin.marketing.discounts.update', $discount), [
            'code' => 'PROMO20',
            'name' => 'Descuento 20%',
            'description' => 'Descuento actualizado',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'minimum_order_amount' => 40.00,
            'usage_limit' => 100,
            'usage_limit_per_user' => 2,
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'expires_at' => now()->addDays(5)->format('Y-m-d\TH:i'),
        ]);

    $response->assertRedirect();
    $this->assertEquals('PROMO20', $discount->fresh()->code);
    $this->assertEquals('Descuento 20%', $discount->fresh()->name);
    $this->assertEquals(20.00, $discount->fresh()->discount_value);
    $this->assertEquals(40.00, $discount->fresh()->minimum_order_amount);
});

test('super admin can delete discount coupon', function () {
    $discount = Discount::create([
        'created_by_user_id' => $this->adminUser->id,
        'code' => 'BORRARME',
        'name' => 'Para Borrar',
        'type' => Discount::TYPE_COUPON,
        'discount_type' => 'free_delivery',
        'discount_value' => 0,
        'applies_to' => Discount::APPLIES_TO_ORDER,
        'minimum_order_amount' => 10.00,
        'usage_limit' => 50,
        'usage_limit_per_user' => 1,
        'starts_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->delete(route('admin.marketing.discounts.destroy', $discount));

    $response->assertRedirect();
    $this->assertNull(Discount::find($discount->id));
});
