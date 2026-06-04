<?php

namespace Database\Factories;

use App\Models\DriverProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DriverProfile>
 */
class DriverProfileFactory extends Factory
{
    protected $model = DriverProfile::class;

    public function definition(): array
    {
        $vehicleBrands = ['Honda', 'Yamaha', 'Suzuki', 'Bajaj', 'TVS', 'Toyota'];

        return [
            'user_id' => User::factory(),
            'vehicle_type' => fake()->randomElement(DriverProfile::vehicleTypes()),
            'license_number' => fake()->unique()->bothify('??-########'),
            'vehicle_brand' => fake()->randomElement($vehicleBrands),
            'vehicle_model' => fake()->word(),
            'vehicle_color' => fake()->safeColorName(),
            'license_plate' => fake()->unique()->bothify('##-####'),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->numerify('9########'),
            'accepts_cash_payments' => fake()->boolean(90),
            'rating_average' => fake()->randomFloat(2, 4.0, 5.0),
            'total_deliveries' => fake()->numberBetween(0, 150),
            'status' => DriverProfile::STATUS_ACTIVE,
            'verified_at' => now(),
        ];
    }
}
