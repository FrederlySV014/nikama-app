<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'device_type',
    'fcm_token',
    'device_name',
    'is_active',
    'last_used_at',
])]
class UserDevice extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_devices';

    public const TYPE_ANDROID = 'android';

    public const TYPE_IOS = 'ios';

    public const TYPE_WEB = 'web';

    /**
     * Get the device types.
     *
     * @return array<int, string>
     */
    public static function deviceTypes(): array
    {
        return [
            self::TYPE_ANDROID,
            self::TYPE_IOS,
            self::TYPE_WEB,
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
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the device.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
