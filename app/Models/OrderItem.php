<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_id',
    'business_id',
    'product_id',
    'product_combo_id',
    'product_name',
    'unit_price',
    'quantity',
    'product_description',
    'product_image_url',
    'subtotal',
    'notes',
])]
class OrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'order_items';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'float',
            'quantity' => 'integer',
            'subtotal' => 'float',
        ];
    }

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the business associated with the item.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the product associated with the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product combo associated with the item.
     */
    public function combo(): BelongsTo
    {
        return $this->belongsTo(ProductCombo::class, 'product_combo_id');
    }

    /**
     * Get the options chosen for this item.
     */
    public function options(): HasMany
    {
        return $this->hasMany(OrderItemOption::class);
    }
}
