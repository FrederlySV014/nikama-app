<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id',
    'commission_type',
    'commission_value',
    'is_active',
    'starts_at',
    'ends_at',
])]
class BusinessCommission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'business_commissions';

    public const TYPE_PERCENTAGE = 'percentage';

    public const TYPE_FIXED = 'fixed';

    /**
     * Get commission models types.
     *
     * @return array<int, string>
     */
    public static function commissionTypes(): array
    {
        return [
            self::TYPE_PERCENTAGE,
            self::TYPE_FIXED,
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
            'commission_value' => 'float',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * Get the business that owns this commission config.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
