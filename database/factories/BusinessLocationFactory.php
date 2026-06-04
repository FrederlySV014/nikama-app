<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\BusinessLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessLocation>
 */
class BusinessLocationFactory extends Factory
{
    protected $model = BusinessLocation::class;

    public function definition(): array
    {
        $chiclayoDistricts = ['Chiclayo', 'Jose Leonardo Ortiz', 'La Victoria', 'Pimentel'];

        return [
            'business_id' => Business::factory(),
            'name' => 'Sede '.fake()->streetName(),
            'address' => fake()->streetAddress(),
            'reference' => 'Cerca de '.fake()->company(),
            'province' => 'Chiclayo',
            'district' => fake()->randomElement($chiclayoDistricts),
            'department' => 'Lambayeque',
            'country' => 'Peru',
            'postal_code' => '14001',
            // Coords around Chiclayo centre (-6.7719, -79.8441)
            'latitude' => -6.7719 + fake()->randomFloat(7, -0.03, 0.03),
            'longitude' => -79.8441 + fake()->randomFloat(7, -0.03, 0.03),
            'location_phone' => fake()->numerify('9########'),
            'delivery_radius_km' => fake()->randomFloat(2, 2.0, 7.0),
            'delivery_fee' => fake()->randomFloat(2, 3.00, 8.00),
            'estimated_delivery_time_minutes' => fake()->randomElement([25, 30, 40, 50]),
            'minimum_delivery_amount' => fake()->randomElement([0.00, 10.00, 15.00]),
            'is_main' => false,
            'is_active' => true,
        ];
    }
}
