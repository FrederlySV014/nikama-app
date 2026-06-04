<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_location_id',
    'exception_date',
    'is_closed',
    'open_time',
    'close_time',
    'reason',
])]
class BusinessHolidayException extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'business_holiday_exceptions';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exception_date' => 'date',
            'is_closed' => 'boolean',
        ];
    }

    /**
     * Get the business location that owns this exception.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }
}
