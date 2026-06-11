<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable([
    'business_id',
    'category_id',
    'is_active',
    'sort_order',
])]
class BusinessCategory extends Pivot
{
    use HasUuids;

    protected $table = 'business_categories';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the business that owns the association.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the category that owns the association.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
