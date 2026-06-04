<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_location_id',
    'day_of_week',
    'opening_time',
    'closing_time',
    'is_24_hours',
    'is_closed',
])]
class BusinessLocationHour extends Model
{
    use HasUuids;

    public const DAY_MONDAY = 'monday';

    public const DAY_TUESDAY = 'tuesday';

    public const DAY_WEDNESDAY = 'wednesday';

    public const DAY_THURSDAY = 'thursday';

    public const DAY_FRIDAY = 'friday';

    public const DAY_SATURDAY = 'saturday';

    public const DAY_SUNDAY = 'sunday';

    /**
     * Get all days of the week.
     *
     * @return array<int, string>
     */
    public static function daysOfWeek(): array
    {
        return [
            self::DAY_MONDAY,
            self::DAY_TUESDAY,
            self::DAY_WEDNESDAY,
            self::DAY_THURSDAY,
            self::DAY_FRIDAY,
            self::DAY_SATURDAY,
            self::DAY_SUNDAY,
        ];
    }

    protected $table = 'business_location_hours';

    protected function casts(): array
    {
        return [
            'is_24_hours' => 'boolean',
            'is_closed' => 'boolean',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }
}
