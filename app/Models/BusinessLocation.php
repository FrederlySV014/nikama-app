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
    'business_id',
    'name',
    'address',
    'reference',
    'province',
    'district',
    'department',
    'country',
    'postal_code',
    'latitude',
    'longitude',
    'location_phone',
    'delivery_radius_km',
    'delivery_fee',
    'estimated_delivery_time_minutes',
    'minimum_delivery_amount',
    'is_main',
    'is_active',
])]
class BusinessLocation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'business_locations';

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'delivery_radius_km' => 'float',
            'delivery_fee' => 'float',
            'estimated_delivery_time_minutes' => 'integer',
            'minimum_delivery_amount' => 'float',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function hours(): HasMany
    {
        return $this->hasMany(BusinessLocationHour::class);
    }

    /**
     * Get holiday and emergency opening exceptions for this location.
     */
    public function holidayExceptions(): HasMany
    {
        return $this->hasMany(BusinessHolidayException::class);
    }

    /**
     * Get the delivery zones mapped to this business location.
     */
    public function deliveryZones(): HasMany
    {
        return $this->hasMany(DeliveryZone::class);
    }
}
