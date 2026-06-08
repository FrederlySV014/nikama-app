<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'parent_id',
    'name',
    'slug',
    'description',
    'icon',
    'image_url',
    'sort_order',
    'is_active',
])]
class Category extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the subcategories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the businesses that belong to the category.
     */
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_categories')
            ->withPivot('is_active', 'sort_order')
            ->withTimestamps();
    }

    /**
     * Get the products that belong to the category.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Get the category URL path (nested parent-child slugs).
     */
    public function getUrlPathAttribute(): string
    {
        $path = $this->slug;
        $parent = $this->parent;
        while ($parent) {
            $path = $parent->slug . '/' . $path;
            $parent = $parent->parent;
        }

        return $path;
    }

    /**
     * Get all descendant category IDs recursively.
     *
     * @return array<int, string>
     */
    public function getAllDescendantIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children()->where('is_active', true)->get() as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }

        return $ids;
    }
}
