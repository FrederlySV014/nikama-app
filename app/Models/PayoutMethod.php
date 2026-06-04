<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'owner_type',
    'owner_id',
    'type',
    'provider_name',
    'account_number',
    'cci_number',
    'holder_name',
    'holder_dni',
    'is_default',
])]
class PayoutMethod extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'payout_methods';

    public const TYPE_BANK_ACCOUNT = 'bank_account';

    public const TYPE_YAPE = 'yape';

    public const TYPE_PLIN = 'plin';

    /**
     * Get payout method types.
     *
     * @return array<int, string>
     */
    public static function payoutTypes(): array
    {
        return [
            self::TYPE_BANK_ACCOUNT,
            self::TYPE_YAPE,
            self::TYPE_PLIN,
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
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the parent owner model (Business or DriverProfile).
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
