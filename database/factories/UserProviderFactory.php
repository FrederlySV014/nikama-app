<?php

namespace Database\Factories;

use App\Models\UserProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProvider>
 */
class UserProviderFactory extends Factory
{
    protected $model = UserProvider::class;

    public function definition(): array
    {
        return [
            'provider' => fake()->randomElement(['google', 'facebook', 'apple']),
            'provider_id' => fake()->uuid(),
            'provider_email' => fake()->safeEmail(),
        ];
    }
}
