<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Subcategoria extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'categoria_id',
        'nombre',
        'descripcion',
        'imagen_url',
        'orden',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'orden' => 'integer',
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

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class)->orderBy('nombre');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}