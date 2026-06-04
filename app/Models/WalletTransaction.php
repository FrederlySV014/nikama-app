<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'holder_type',
    'holder_id',
    'amount',
    'type',
    'transaction_type',
    'reference_id',
    'description',
])]
class WalletTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'wallet_transactions';

    public const TYPE_CREDIT = 'credit';

    public const TYPE_DEBIT = 'debit';

    /**
     * Get transaction types.
     *
     * @return array<int, string>
     */
    public static function transactionTypes(): array
    {
        return [
            self::TYPE_CREDIT,
            self::TYPE_DEBIT,
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
        ];
    }

    /**
     * Get the parent holder model (User, Business, or DriverProfile).
     */
    public function holder(): MorphTo
    {
        return $this->morphTo();
    }
}
