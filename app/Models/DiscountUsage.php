<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'discount_id',
    'user_id',
    'order_id',
    'discount_applied',
    'used_at',
])]
class DiscountUsage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'discount_usages';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_applied' => 'float',
            'used_at' => 'datetime',
        ];
    }

    /**
     * Get the discount applied.
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Get the user who used the discount.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order where the discount was applied.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
