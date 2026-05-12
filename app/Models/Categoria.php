<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Categoria extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
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

    public function subcategorias(): HasMany
    {
        return $this->hasMany(Subcategoria::class)->orderBy('orden');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}