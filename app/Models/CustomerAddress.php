<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'label',
    'address',
    'address_type',
    'reference',
    'delivery_notes',
    'contact_name',
    'contact_phone',
    'province',
    'district',
    'department',
    'country',
    'postal_code',
    'latitude',
    'longitude',
    'is_default',
    'is_active',
])]
class CustomerAddress extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'customer_addresses';

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the orders associated with this delivery address.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
