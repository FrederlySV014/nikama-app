<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'driver_profile_id',
    'document_type',
    'document_url',
    'status',
    'rejected_reason',
    'expires_at',
])]
class DriverDocument extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'driver_documents';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const TYPE_LICENSE = 'license';

    public const TYPE_SOAT = 'soat';

    public const TYPE_IDENTITY_CARD = 'identity_card';

    /**
     * Get all document statuses.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
        ];
    }

    /**
     * Get all document types.
     *
     * @return array<int, string>
     */
    public static function documentTypes(): array
    {
        return [
            self::TYPE_LICENSE,
            self::TYPE_SOAT,
            self::TYPE_IDENTITY_CARD,
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
            'expires_at' => 'date',
        ];
    }

    /**
     * Get the driver profile that owns this document.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(DriverProfile::class, 'driver_profile_id');
    }
}
