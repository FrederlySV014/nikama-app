<?php

namespace Database\Factories;

use App\Models\DriverLiveLocation;
use App\Models\DriverProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DriverLiveLocation>
 */
class DriverLiveLocationFactory extends Factory
{
    protected $model = DriverLiveLocation::class;

    public function definition(): array
    {
        return [
            'driver_profile_id' => DriverProfile::factory(),
            // Coords around Chiclayo centre (-6.7719, -79.8441)
            'latitude' => -6.7719 + fake()->randomFloat(7, -0.03, 0.03),
            'longitude' => -79.8441 + fake()->randomFloat(7, -0.03, 0.03),
            'is_online' => fake()->boolean(80),
            'is_available' => fake()->boolean(60),
            'last_location_updated_at' => now(),
            'last_online_at' => now(),
        ];
    }
}
