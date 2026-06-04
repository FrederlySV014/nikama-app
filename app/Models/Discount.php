<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'business_id',
    'created_by_user_id',
    'code',
    'name',
    'description',
    'type',
    'discount_type',
    'discount_value',
    'applies_to',
    'rules',
    'minimum_order_amount',
    'maximum_discount_amount',
    'usage_limit',
    'used_count',
    'usage_limit_per_user',
    'starts_at',
    'expires_at',
    'is_active',
])]
class Discount extends Model
{
    use HasFactory, HasUuids;

    public const TYPE_COUPON = 'coupon';

    public const TYPE_AUTOMATIC = 'automatic';

    public const DISCOUNT_TYPE_PERCENTAGE = 'percentage';

    public const DISCOUNT_TYPE_FIXED = 'fixed';

    public const DISCOUNT_TYPE_FREE_DELIVERY = 'free_delivery';

    public const APPLIES_TO_ORDER = 'order';

    public const APPLIES_TO_DELIVERY = 'delivery';

    public const APPLIES_TO_PRODUCT = 'product';

    public const APPLIES_TO_CATEGORY = 'category';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rules' => 'array',
            'discount_value' => 'float',
            'minimum_order_amount' => 'float',
            'maximum_discount_amount' => 'float',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'usage_limit_per_user' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the business that owns this discount (if applicable).
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user who created this discount.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the usages of this discount.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }
}
