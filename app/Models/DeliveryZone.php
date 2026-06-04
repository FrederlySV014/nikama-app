<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_location_id',
    'name',
    'polygon_coordinates',
    'delivery_fee',
    'minimum_order_amount',
    'is_active',
])]
class DeliveryZone extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'delivery_zones';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'polygon_coordinates' => 'array',
            'delivery_fee' => 'float',
            'minimum_order_amount' => 'float',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the business location associated with this delivery zone.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }
}
