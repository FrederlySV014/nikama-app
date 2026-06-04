<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'product_option_group_id',
    'name',
    'additional_price',
    'is_available',
    'sort_order',
])]
class ProductOption extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'product_options';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'additional_price' => 'float',
            'is_available' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the option group that contains this option.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(ProductOptionGroup::class, 'product_option_group_id');
    }

    /**
     * Get all cart item option selections referencing this option.
     */
    public function cartItemOptions(): HasMany
    {
        return $this->hasMany(CartItemOption::class);
    }

    /**
     * Get all order item option selections referencing this option.
     */
    public function orderItemOptions(): HasMany
    {
        return $this->hasMany(OrderItemOption::class);
    }
}
