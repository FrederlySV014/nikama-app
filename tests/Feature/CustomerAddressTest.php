<?php

use App\Models\CustomerAddress;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Create a customer user
    $this->customer = User::factory()->create();
    $customerRole = Role::where('slug', Role::CUSTOMER)->first();
    $this->customer->roles()->attach($customerRole->id);

    // Create another customer user
    $this->otherCustomer = User::factory()->create();
    $this->otherCustomer->roles()->attach($customerRole->id);

    // Seed active districts to allow existing test values (like Lince, Surco, San Isidro, Miraflores, San Borja, Lima)
    SystemSetting::updateOrCreate(
        ['key' => 'active_districts'],
        ['value' => json_encode([
            'Lambayeque|Chiclayo|Chiclayo',
            'Lambayeque|Chiclayo|José Leonardo Ortiz',
            'Lambayeque|Chiclayo|La Victoria',
            'Lambayeque|Chiclayo|Pimentel',
            'Lima|Lima|Miraflores',
            'Lima|Lima|San Isidro',
            'Lima|Lima|Lince',
            'Lima|Lima|Surco',
            'Lima|Lima|San Borja',
            'Lima|Lima|Lima',
            'Lambayeque|Chiclayo|Trabajo Temporal',
            'Lambayeque|Chiclayo|Universidad',
            'Lambayeque|Chiclayo|Casa de playa',
        ])]
    );
});

test('guests are redirected to login when managing addresses', function () {
    $this->get(route('profile.addresses.index'))
        ->assertRedirect(route('login'));

    $this->post(route('profile.addresses.store'), [])
        ->assertRedirect(route('login'));
});

test('authenticated users can view their address book', function () {
    $address = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'label' => 'Casa',
        'address' => 'Calle Falsa 123',
        'department' => 'Lima',
        'province' => 'Lima',
        'district' => 'Miraflores',
        'is_default' => true,
    ]);

    $response = $this->actingAs($this->customer)
        ->get(route('profile.addresses.index'));

    $response->assertStatus(200)
        ->assertViewIs('public.profile.addresses.index')
        ->assertViewHas('addresses')
        ->assertSee('Calle Falsa 123')
        ->assertSee('Casa');
});

test('authenticated users can create a new address and it becomes default if it is the first', function () {
    $response = $this->actingAs($this->customer)
        ->post(route('profile.addresses.store'), [
            'label' => 'Oficina',
            'address' => 'Av. Javier Prado 456',
            'district' => 'San Isidro',
            'province' => 'Lima',
            'department' => 'Lima',
            'contact_name' => 'Juan Perez',
            'contact_phone' => '999888777',
            'latitude' => -12.093415,
            'longitude' => -77.014235,
        ]);

    $response->assertRedirect(route('profile.addresses.index'));

    $this->assertDatabaseHas('customer_addresses', [
        'user_id' => $this->customer->id,
        'label' => 'Oficina',
        'address' => 'Av. Javier Prado 456',
        'latitude' => -12.093415,
        'longitude' => -77.014235,
        'is_default' => true,
    ]);
});

test('subsequent addresses do not default unless specified', function () {
    // First address (should auto default)
    $first = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'is_default' => true,
        'is_active' => true,
    ]);

    // Second address (without default)
    $response = $this->actingAs($this->customer)
        ->post(route('profile.addresses.store'), [
            'label' => 'Trabajo',
            'address' => 'Av. Arequipa 789',
            'district' => 'Lince',
            'is_default' => '0',
        ]);

    $response->assertRedirect(route('profile.addresses.index'));

    $this->assertDatabaseHas('customer_addresses', [
        'user_id' => $this->customer->id,
        'label' => 'Trabajo',
        'is_default' => false,
    ]);

    $this->assertTrue($first->fresh()->is_default);
});

test('setting a new default address clears previous defaults for the user', function () {
    $first = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'label' => 'Casa',
        'is_default' => true,
        'is_active' => true,
    ]);

    // Create second address as default
    $this->actingAs($this->customer)
        ->post(route('profile.addresses.store'), [
            'label' => 'Oficina',
            'address' => 'Av. Javier Prado 456',
            'district' => 'San Isidro',
            'is_default' => '1',
        ]);

    $this->assertFalse($first->fresh()->is_default);

    $second = CustomerAddress::where('user_id', $this->customer->id)->where('label', 'Oficina')->first();
    $this->assertTrue($second->is_default);
});

test('authenticated users can update their addresses', function () {
    $address = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'label' => 'Antigua',
        'address' => 'Calle Vieja 999',
        'department' => 'Lima',
        'province' => 'Lima',
        'district' => 'Surco',
    ]);

    $response = $this->actingAs($this->customer)
        ->put(route('profile.addresses.update', $address), [
            'label' => 'Nueva',
            'address' => 'Calle Nueva 111',
            'district' => 'Surco',
            'province' => 'Lima',
            'department' => 'Lima',
            'contact_name' => 'Maria',
            'contact_phone' => '999111222',
            'latitude' => -12.115423,
            'longitude' => -77.031548,
            'is_default' => '0',
        ]);

    $response->assertRedirect(route('profile.addresses.index'));

    $this->assertDatabaseHas('customer_addresses', [
        'id' => $address->id,
        'label' => 'Nueva',
        'address' => 'Calle Nueva 111',
        'latitude' => -12.115423,
        'longitude' => -77.031548,
    ]);
});

test('authenticated users can set an address as default', function () {
    $first = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'is_default' => true,
        'is_active' => true,
    ]);

    $second = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'is_default' => false,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->customer)
        ->post(route('profile.addresses.default', $second));

    $response->assertRedirect(route('profile.addresses.index'));
    $this->assertFalse($first->fresh()->is_default);
    $this->assertTrue($second->fresh()->is_default);
});

test('authenticated users can delete an address', function () {
    $address = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'is_default' => false,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->customer)
        ->delete(route('profile.addresses.destroy', $address));

    $response->assertRedirect(route('profile.addresses.index'));

    // Check it is soft-deleted
    $this->assertSoftDeleted('customer_addresses', [
        'id' => $address->id,
    ]);

    $this->assertFalse($address->fresh()->is_active);
});

test('deleting a default address makes another active address default', function () {
    $first = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'is_default' => true,
        'is_active' => true,
    ]);

    $second = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'is_default' => false,
        'is_active' => true,
    ]);

    $this->actingAs($this->customer)
        ->delete(route('profile.addresses.destroy', $first));

    $this->assertTrue($second->fresh()->is_default);
});

test('authenticated users cannot modify another users addresses', function () {
    $address = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'is_active' => true,
    ]);

    $this->actingAs($this->otherCustomer)
        ->put(route('profile.addresses.update', $address), [
            'label' => 'Hack',
            'address' => 'Av. Hackers 123',
            'district' => 'Lima',
        ])
        ->assertStatus(403);

    $this->actingAs($this->otherCustomer)
        ->delete(route('profile.addresses.destroy', $address))
        ->assertStatus(403);

    $this->actingAs($this->otherCustomer)
        ->post(route('profile.addresses.default', $address))
        ->assertStatus(403);
});

test('storing an address via AJAX returns JSON response', function () {
    $response = $this->actingAs($this->customer)
        ->postJson(route('profile.addresses.store'), [
            'label' => 'Trabajo AJAX',
            'address' => 'Av. Guardia Civil 123',
            'district' => 'San Borja',
            'province' => 'Lima',
            'department' => 'Lima',
            'latitude' => -12.105432,
            'longitude' => -77.021543,
            'is_default' => '1',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Dirección agregada correctamente.',
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'address' => [
                'id',
                'label',
                'address',
                'district',
                'province',
                'department',
                'latitude',
                'longitude',
                'is_default',
            ],
        ]);
});

test('guests cannot view address create or edit form', function () {
    $this->get(route('profile.addresses.create'))
        ->assertRedirect(route('login'));

    $address = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
    ]);

    $this->get(route('profile.addresses.edit', $address))
        ->assertRedirect(route('login'));
});

test('authenticated users can view address create and edit form', function () {
    $this->actingAs($this->customer)
        ->get(route('profile.addresses.create'))
        ->assertStatus(200)
        ->assertViewIs('public.profile.addresses.create');

    $address = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
    ]);

    $this->actingAs($this->customer)
        ->get(route('profile.addresses.edit', $address))
        ->assertStatus(200)
        ->assertViewIs('public.profile.addresses.edit')
        ->assertViewHas('address');
});

test('authenticated users cannot view edit form of another users address', function () {
    $address = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
    ]);

    $this->actingAs($this->otherCustomer)
        ->get(route('profile.addresses.edit', $address))
        ->assertStatus(403);
});

test('address creation and updates persist extra fields', function () {
    $this->actingAs($this->customer)
        ->post(route('profile.addresses.store'), [
            'label' => 'Casa Completa',
            'address' => 'Av. Larco 456',
            'address_type' => 'Universidad',
            'reference' => 'Cerca a Larcomar',
            'delivery_notes' => 'Tocar dos veces el timbre',
            'district' => 'Miraflores',
            'province' => 'Lima',
            'department' => 'Lima',
            'country' => 'Peru',
            'postal_code' => '15074',
            'contact_name' => 'Renato',
            'contact_phone' => '987654321',
            'latitude' => -12.1234,
            'longitude' => -77.0123,
            'is_default' => '1',
        ]);

    $this->assertDatabaseHas('customer_addresses', [
        'user_id' => $this->customer->id,
        'label' => 'Casa Completa',
        'address' => 'Av. Larco 456',
        'address_type' => 'Universidad',
        'reference' => 'Cerca a Larcomar',
        'delivery_notes' => 'Tocar dos veces el timbre',
        'postal_code' => '15074',
        'country' => 'Peru',
        'contact_name' => 'Renato',
        'contact_phone' => '987654321',
        'latitude' => -12.1234,
        'longitude' => -77.0123,
        'is_default' => true,
    ]);

    $address = CustomerAddress::where('user_id', $this->customer->id)->first();

    $this->actingAs($this->customer)
        ->put(route('profile.addresses.update', $address), [
            'label' => 'Trabajo Modificado',
            'address' => 'Av. Arequipa 123',
            'address_type' => 'Casa de playa',
            'reference' => 'Frente al parque',
            'delivery_notes' => 'Dejar en recepción',
            'district' => 'Lince',
            'province' => 'Lima',
            'department' => 'Lima',
            'country' => 'Ecuador',
            'postal_code' => '15046',
            'contact_name' => 'Carlos',
            'contact_phone' => '911222333',
            'latitude' => -12.0811,
            'longitude' => -77.0322,
            'is_default' => '0',
        ]);

    $this->assertDatabaseHas('customer_addresses', [
        'id' => $address->id,
        'label' => 'Trabajo Modificado',
        'address' => 'Av. Arequipa 123',
        'address_type' => 'Casa de playa',
        'reference' => 'Frente al parque',
        'delivery_notes' => 'Dejar en recepción',
        'postal_code' => '15046',
        'country' => 'Ecuador',
        'contact_name' => 'Carlos',
        'contact_phone' => '911222333',
        'latitude' => -12.0811,
        'longitude' => -77.0322,
        'is_default' => false,
    ]);
});

test('authenticated users cannot create an address in a district without coverage', function () {
    // Disable Surco (make only Chiclayo active)
    SystemSetting::updateOrCreate(
        ['key' => 'active_districts'],
        ['value' => json_encode(['Lambayeque|Chiclayo|Chiclayo'])]
    );

    $response = $this->actingAs($this->customer)
        ->from(route('profile.addresses.create'))
        ->post(route('profile.addresses.store'), [
            'label' => 'Casa',
            'address' => 'Av. Javier Prado 123',
            'district' => 'Miraflores', // Inactive district
        ]);

    $response->assertRedirect(route('profile.addresses.create'));
    $response->assertSessionHasErrors('district');
});

test('authenticated users cannot update an address to a district without coverage', function () {
    // Enable Miraflores initially
    SystemSetting::updateOrCreate(
        ['key' => 'active_districts'],
        ['value' => json_encode(['Lambayeque|Chiclayo|Chiclayo', 'Lima|Lima|Miraflores'])]
    );

    $address = CustomerAddress::factory()->create([
        'user_id' => $this->customer->id,
        'label' => 'Casa',
        'address' => 'Av. Javier Prado 123',
        'department' => 'Lima',
        'province' => 'Lima',
        'district' => 'Miraflores',
    ]);

    // Now disable Miraflores (only Chiclayo active)
    SystemSetting::updateOrCreate(
        ['key' => 'active_districts'],
        ['value' => json_encode(['Lambayeque|Chiclayo|Chiclayo'])]
    );

    $response = $this->actingAs($this->customer)
        ->from(route('profile.addresses.edit', $address))
        ->put(route('profile.addresses.update', $address), [
            'label' => 'Casa Actualizada',
            'address' => 'Av. Javier Prado 123',
            'district' => 'Miraflores', // Now inactive
        ]);

    $response->assertRedirect(route('profile.addresses.edit', $address));
    $response->assertSessionHasErrors('district');
});
