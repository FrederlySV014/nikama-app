<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id',
    'name',
    'slug',
    'description',
    'price',
    'compare_price',
    'sku',
    'stock_quantity',
    'track_stock',
    'allow_backorder',
    'status',
    'is_featured',
    'requires_preparation',
    'preparation_time_minutes',
    'weight_grams',
    'main_image_url',
    'rating_average',
    'total_reviews',
    'total_sales',
    'views_count',
    'is_available',
    'published_at',
])]
class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_OUT_OF_STOCK = 'out_of_stock';

    public const STATUS_SUSPENDED = 'suspended';

    /**
     * Get all product statuses.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_OUT_OF_STOCK,
            self::STATUS_SUSPENDED,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'float',
            'compare_price' => 'float',
            'stock_quantity' => 'integer',
            'track_stock' => 'boolean',
            'allow_backorder' => 'boolean',
            'is_featured' => 'boolean',
            'requires_preparation' => 'boolean',
            'preparation_time_minutes' => 'integer',
            'weight_grams' => 'float',
            'rating_average' => 'float',
            'total_reviews' => 'integer',
            'total_sales' => 'integer',
            'views_count' => 'integer',
            'is_available' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the business that owns the product.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the categories that belong to the product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories')
            ->using(ProductCategory::class)
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the combos that contain the product.
     */
    public function combos(): BelongsToMany
    {
        return $this->belongsToMany(ProductCombo::class, 'product_combo_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get the cart items referencing this product.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the order items referencing this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the option groups for the product.
     */
    public function optionGroups(): HasMany
    {
        return $this->hasMany(ProductOptionGroup::class);
    }

    /**
     * Get the reviews for this product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }
}
