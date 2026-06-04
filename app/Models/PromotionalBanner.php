<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'image_url',
    'action_type',
    'action_id',
    'action_url',
    'sort_order',
    'starts_at',
    'expires_at',
    'is_active',
])]
class PromotionalBanner extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'promotional_banners';

    public const ACTION_OPEN_BUSINESS = 'open_business';

    public const ACTION_OPEN_PRODUCT = 'open_product';

    public const ACTION_OPEN_CATEGORY = 'open_category';

    public const ACTION_EXTERNAL_LINK = 'external_link';

    /**
     * Get banner action types.
     *
     * @return array<int, string>
     */
    public static function actionTypes(): array
    {
        return [
            self::ACTION_OPEN_BUSINESS,
            self::ACTION_OPEN_PRODUCT,
            self::ACTION_OPEN_CATEGORY,
            self::ACTION_EXTERNAL_LINK,
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
            'sort_order' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
