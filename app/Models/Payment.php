<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_id',
    'payment_method',
    'provider',
    'status',
    'amount',
    'transaction_id',
    'failed_at',
    'refunded_at',
    'provider_response',
    'paid_at',
])]
class Payment extends Model
{
    use HasFactory, HasUuids;

    public const METHOD_CASH = 'cash';

    public const METHOD_CARD = 'card';

    public const METHOD_YAPE = 'yape';

    public const METHOD_PLIN = 'plin';

    public const PROVIDER_MERCADOPAGO = 'mercadopago';

    public const PROVIDER_STRIPE = 'stripe';

    public const PROVIDER_IZIPAY = 'izipay';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REFUNDED = 'refunded';

    /**
     * Get all payment methods.
     *
     * @return array<int, string>
     */
    public static function paymentMethods(): array
    {
        return [
            self::METHOD_CASH,
            self::METHOD_CARD,
            self::METHOD_YAPE,
            self::METHOD_PLIN,
        ];
    }

    /**
     * Get all providers.
     *
     * @return array<int, string>
     */
    public static function providers(): array
    {
        return [
            self::PROVIDER_MERCADOPAGO,
            self::PROVIDER_STRIPE,
            self::PROVIDER_IZIPAY,
        ];
    }

    /**
     * Get all payment statuses.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PAID,
            self::STATUS_FAILED,
            self::STATUS_REFUNDED,
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
            'amount' => 'float',
            'failed_at' => 'datetime',
            'refunded_at' => 'datetime',
            'paid_at' => 'datetime',
            'provider_response' => 'array',
        ];
    }

    /**
     * Get the order that owns the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the refunds processed for the payment.
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class);
    }
}
