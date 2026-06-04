<?php

namespace App\Models;

use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'name',
    'slug',
    'description',
])]
class Role extends Model
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory, HasUuids;

    /*
     * System roles:
     * - super_admin
     * - seller
     * - driver
     * - customer
     */
    public const SUPER_ADMIN = 'super_admin';

    public const SELLER = 'seller';

    public const DRIVER = 'driver';

    public const CUSTOMER = 'customer';

    /**
     * Get all system role slugs.
     */
    public static function systemRoles(): array
    {
        return [
            self::SUPER_ADMIN,
            self::SELLER,
            self::DRIVER,
            self::CUSTOMER,
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withTimestamps();
    }
}
