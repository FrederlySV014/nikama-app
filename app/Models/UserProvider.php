<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'provider',
    'provider_id',
    'provider_email',
])]
class UserProvider extends Model
{
    use HasUuids;

    /*
     * Supported OAuth providers:
     * - google
     * - facebook
     * - apple
     */
    public const PROVIDER_GOOGLE = 'google';

    public const PROVIDER_FACEBOOK = 'facebook';

    public const PROVIDER_APPLE = 'apple';

    /**
     * Get all supported provider slugs.
     */
    public static function providers(): array
    {
        return [
            self::PROVIDER_GOOGLE,
            self::PROVIDER_FACEBOOK,
            self::PROVIDER_APPLE,
        ];
    }

    protected $table = 'user_providers';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
