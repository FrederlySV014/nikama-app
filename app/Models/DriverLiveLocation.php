<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'driver_profile_id',
    'latitude',
    'longitude',
    'is_online',
    'is_available',
    'last_location_updated_at',
    'last_online_at',
])]
class DriverLiveLocation extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'driver_live_locations';

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_online' => 'boolean',
            'is_available' => 'boolean',
            'last_location_updated_at' => 'datetime',
            'last_online_at' => 'datetime',
        ];
    }

    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(DriverProfile::class);
    }
}
