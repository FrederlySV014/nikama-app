<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Producto extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'subcategoria_id',
        'codigo_sku',
        'codigo_barras',
        'nombre',
        'descripcion',
        'marca',
        'modelo',
        'color',
        'peso_gramos',
        'garantia_meses',
        'precio_compra',
        'precio_venta',
        'precio_oferta',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
        'estado_producto',
        'is_available',
        'is_active',
        'imagen_url',
        'imagenes_extra',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'imagenes_extra' => 'array',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'precio_oferta' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function subcategoria(): BelongsTo
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_available', true);
    }

    public function scopeWithStock($query)
    {
        return $query->where('stock_actual', '>', 0);
    }
}