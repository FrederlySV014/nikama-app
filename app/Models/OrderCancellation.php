<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'cancelled_by_type',
    'cancelled_by_id',
    'reason_code',
    'comment',
    'penalty_applied',
    'penalty_amount',
])]
class OrderCancellation extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'order_cancellations';

    public const BY_CUSTOMER = 'customer';

    public const BY_BUSINESS = 'business';

    public const BY_DRIVER = 'driver';

    public const BY_SYSTEM = 'system';

    public const BY_ADMIN = 'admin';

    /**
     * Get actors who can cancel orders.
     *
     * @return array<int, string>
     */
    public static function cancelActors(): array
    {
        return [
            self::BY_CUSTOMER,
            self::BY_BUSINESS,
            self::BY_DRIVER,
            self::BY_SYSTEM,
            self::BY_ADMIN,
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
            'penalty_applied' => 'boolean',
            'penalty_amount' => 'float',
        ];
    }

    /**
     * Get the order associated with the cancellation.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
