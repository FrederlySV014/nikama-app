<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'birth_date',
    'gender',
    'accept_marketing_emails',
    'accept_notifications',
])]
class CustomerProfile extends Model
{
    use HasFactory, HasUuids;

    public const GENDER_MALE = 'male';

    public const GENDER_FEMALE = 'female';

    public const GENDER_OTHER = 'other';

    /**
     * Get all gender options.
     *
     * @return array<int, string>
     */
    public static function genders(): array
    {
        return [
            self::GENDER_MALE,
            self::GENDER_FEMALE,
            self::GENDER_OTHER,
        ];
    }

    protected $table = 'customer_profiles';

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'accept_marketing_emails' => 'boolean',
            'accept_notifications' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
