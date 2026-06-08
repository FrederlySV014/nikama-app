<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'business_id' => Business::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 5.00, 150.00),
            'compare_price' => function (array $attributes) {
                return fake()->boolean(40) ? $attributes['price'] * 1.2 : null;
            },
            'sku' => strtoupper(fake()->unique()->lexify('???-???-???')),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'track_stock' => true,
            'allow_backorder' => false,
            'status' => Product::STATUS_DRAFT,
            'is_featured' => false,
            'requires_preparation' => true,
            'preparation_time_minutes' => fake()->numberBetween(5, 60),
            'weight_grams' => fake()->randomFloat(2, 50, 2000),
            'main_image_url' => fake()->imageUrl(400, 400, 'product', true),
            'rating_average' => 0.00,
            'total_reviews' => 0,
            'total_sales' => 0,
            'views_count' => 0,
            'is_available' => true,
            'published_at' => null,
        ];
    }
}
