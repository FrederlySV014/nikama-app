<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'customer_address_id',
    'order_number',
    'status',
    'payment_status',
    'subtotal',
    'delivery_fee',
    'total',
    'delivery_address',
    'delivery_reference',
    'delivery_latitude',
    'delivery_longitude',
    'confirmed_at',
    'delivered_at',
    'cancelled_at',
    'notes',
])]
class Order extends Model
{
    use HasFactory, HasUuids;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_PREPARING = 'preparing';

    public const STATUS_ON_THE_WAY = 'on_the_way';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_STATUS_PENDING = 'pending';

    public const PAYMENT_STATUS_PAID = 'paid';

    public const PAYMENT_STATUS_FAILED = 'failed';

    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    /**
     * Get all order statuses.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PREPARING,
            self::STATUS_ON_THE_WAY,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Get all payment statuses.
     *
     * @return array<int, string>
     */
    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_STATUS_PENDING,
            self::PAYMENT_STATUS_PAID,
            self::PAYMENT_STATUS_FAILED,
            self::PAYMENT_STATUS_REFUNDED,
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
            'subtotal' => 'float',
            'delivery_fee' => 'float',
            'total' => 'float',
            'delivery_latitude' => 'float',
            'delivery_longitude' => 'float',
            'confirmed_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Get the user that placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the delivery address configuration.
     */
    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments associated with the order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the deliveries associated with the order.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get the status changes history for the order.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Get the driver assignments for the order.
     */
    public function driverAssignments(): HasMany
    {
        return $this->hasMany(DriverAssignment::class);
    }

    /**
     * Get the discount usages associated with the order.
     */
    public function discountUsages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }

    /**
     * Get the product reviews associated with the order.
     */
    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the driver reviews associated with the order.
     */
    public function driverReviews(): HasMany
    {
        return $this->hasMany(DriverReview::class);
    }

    /**
     * Get the refunds processed for the order.
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class);
    }

    /**
     * Get the cancellation log for the order.
     */
    public function cancellation(): HasOne
    {
        return $this->hasOne(OrderCancellation::class);
    }

    /**
     * Get support issues reported for the order.
     */
    public function issues(): HasMany
    {
        return $this->hasMany(OrderIssue::class);
    }
}
