<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id',
    'amount',
    'commission_deducted',
    'net_amount',
    'status',
    'transaction_reference',
    'notes',
    'processed_at',
])]
class BusinessPayout extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'business_payouts';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSED = 'processed';

    public const STATUS_FAILED = 'failed';

    /**
     * Get all payout statuses.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSED,
            self::STATUS_FAILED,
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
            'commission_deducted' => 'float',
            'net_amount' => 'float',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Get the business that received this payout.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
