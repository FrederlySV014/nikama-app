<?php

namespace Database\Factories;

use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerAddress>
 */
class CustomerAddressFactory extends Factory
{
    protected $model = CustomerAddress::class;

    public function definition(): array
    {
        $labels = ['Casa', 'Trabajo', 'Depto', 'Mamá', 'Novia'];
        $districts = ['Chiclayo', 'Jose Leonardo Ortiz', 'La Victoria', 'Pimentel'];

        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement($labels),
            'address' => fake()->streetAddress(),
            'address_type' => fake()->randomElement(['house', 'apartment', 'office']),
            'reference' => 'Frente a '.fake()->company(),
            'delivery_notes' => fake()->boolean(40) ? fake()->sentence() : null,
            'contact_name' => fake()->name(),
            'contact_phone' => fake()->numerify('9########'),
            'province' => 'Chiclayo',
            'district' => fake()->randomElement($districts),
            'department' => 'Lambayeque',
            'country' => 'Peru',
            'postal_code' => '14001',
            // Coords around Chiclayo centre (-6.7719, -79.8441)
            'latitude' => -6.7719 + fake()->randomFloat(7, -0.04, 0.04),
            'longitude' => -79.8441 + fake()->randomFloat(7, -0.04, 0.04),
            'is_default' => false,
            'is_active' => true,
        ];
    }
}
