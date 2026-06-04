<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'product_id',
    'name',
    'selection_type',
    'is_required',
    'min_selections',
    'max_selections',
    'sort_order',
])]
class ProductOptionGroup extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'product_option_groups';

    public const SELECTION_SINGLE = 'single';

    public const SELECTION_MULTIPLE = 'multiple';

    /**
     * Get all selection types.
     *
     * @return array<int, string>
     */
    public static function selectionTypes(): array
    {
        return [
            self::SELECTION_SINGLE,
            self::SELECTION_MULTIPLE,
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
            'is_required' => 'boolean',
            'min_selections' => 'integer',
            'max_selections' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the product that owns this option group.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the options in this option group.
     */
    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }
}
