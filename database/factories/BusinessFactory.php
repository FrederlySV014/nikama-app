<?php

namespace Database\Factories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'business_name' => $name,
            'slug' => Str::slug($name),
            'legal_name' => $name.' S.A.C.',
            'ruc' => fake()->unique()->numerify('20#########'),
            'description' => fake()->paragraph(),
            'logo_url' => fake()->imageUrl(200, 200, 'business', true, 'logo'),
            'banner_url' => fake()->imageUrl(800, 400, 'business', true, 'banner'),
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => fake()->numerify('9########'),
            'whatsapp_number' => fake()->numerify('9########'),
            'rating_average' => fake()->randomFloat(2, 3.5, 5.0),
            'total_reviews' => fake()->numberBetween(5, 50),
            'total_orders' => fake()->numberBetween(10, 200),
            'minimum_order_amount' => fake()->randomElement([10.00, 15.00, 20.00, 0.00]),
            'estimated_preparation_time_minutes' => fake()->randomElement([15, 20, 30, 45]),
            'status' => Business::STATUS_APPROVED,
            'rejected_reason' => null,
            'accepts_orders' => true,
            'is_active' => true,
            'offers_delivery' => true,
            'offers_pickup' => true,
            'is_featured' => fake()->boolean(20),
            'facebook_url' => 'https://facebook.com/'.Str::slug($name),
            'instagram_url' => 'https://instagram.com/'.Str::slug($name),
            'website_url' => 'https://'.Str::slug($name).'.pe',
            'verified_at' => now(),
            'approved_at' => now(),
            'suspended_at' => null,
        ];
    }
}
