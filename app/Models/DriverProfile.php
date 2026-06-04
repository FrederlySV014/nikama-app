<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'vehicle_type',
    'license_number',
    'vehicle_brand',
    'vehicle_model',
    'vehicle_color',
    'license_plate',
    'emergency_contact_name',
    'emergency_contact_phone',
    'accepts_cash_payments',
    'rating_average',
    'total_deliveries',
    'status',
    'rejected_reason',
    'verified_at',
])]
class DriverProfile extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public const VEHICLE_BICYCLE = 'bicycle';

    public const VEHICLE_MOTORCYCLE = 'motorcycle';

    public const VEHICLE_CAR = 'car';

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SUSPENDED = 'suspended';

    /**
     * Get all vehicle types.
     *
     * @return array<int, string>
     */
    public static function vehicleTypes(): array
    {
        return [
            self::VEHICLE_BICYCLE,
            self::VEHICLE_MOTORCYCLE,
            self::VEHICLE_CAR,
        ];
    }

    /**
     * Get all driver statuses.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_REJECTED,
            self::STATUS_SUSPENDED,
        ];
    }

    protected $table = 'driver_profiles';

    protected function casts(): array
    {
        return [
            'accepts_cash_payments' => 'boolean',
            'rating_average' => 'float',
            'total_deliveries' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function liveLocation(): HasOne
    {
        return $this->hasOne(DriverLiveLocation::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(DriverLocationLog::class);
    }

    /**
     * Get the deliveries assigned to this driver.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Get driver assignments for this driver.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(DriverAssignment::class);
    }

    /**
     * Get the reviews submitted for this driver.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(DriverReview::class);
    }

    /**
     * Get the payouts issued to this driver.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(DriverPayout::class);
    }

    /**
     * Get the verification documents uploaded by this driver.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(DriverDocument::class);
    }
}
