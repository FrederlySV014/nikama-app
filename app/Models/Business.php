<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_name',
    'slug',
    'legal_name',
    'ruc',
    'description',
    'logo_url',
    'banner_url',
    'contact_email',
    'contact_phone',
    'whatsapp_number',
    'rating_average',
    'total_reviews',
    'total_orders',
    'minimum_order_amount',
    'estimated_preparation_time_minutes',
    'status',
    'rejected_reason',
    'accepts_orders',
    'is_active',
    'offers_delivery',
    'offers_pickup',
    'is_featured',
    'facebook_url',
    'instagram_url',
    'website_url',
    'verified_at',
    'approved_at',
    'suspended_at',
])]
class Business extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SUSPENDED = 'suspended';

    /**
     * Get all business statuses.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_SUSPENDED,
        ];
    }

    protected function casts(): array
    {
        return [
            'rating_average' => 'float',
            'total_reviews' => 'integer',
            'total_orders' => 'integer',
            'minimum_order_amount' => 'float',
            'estimated_preparation_time_minutes' => 'integer',
            'accepts_orders' => 'boolean',
            'is_active' => 'boolean',
            'offers_delivery' => 'boolean',
            'offers_pickup' => 'boolean',
            'is_featured' => 'boolean',
            'verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_users')
            ->withTimestamps();
    }

    public function locations(): HasMany
    {
        return $this->hasMany(BusinessLocation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BusinessReview::class);
    }

    /**
     * Get the categories associated with the business.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'business_categories')
            ->using(BusinessCategory::class)
            ->withPivot('is_active', 'sort_order')
            ->withTimestamps();
    }

    /**
     * Get the products owned by the business.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the combos offered by the business.
     */
    public function combos(): HasMany
    {
        return $this->hasMany(ProductCombo::class);
    }

    /**
     * Get the cart items referencing this business.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the order items referencing this business.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the deliveries starting from this business.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get the discounts offered by this business.
     */
    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }

    /**
     * Get the commission history/configurations for this business.
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(BusinessCommission::class);
    }

    /**
     * Get the payouts issued to this business.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(BusinessPayout::class);
    }
}
