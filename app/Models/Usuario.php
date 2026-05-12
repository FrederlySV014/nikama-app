<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Usuario extends Model
{
    use SoftDeletes;

    protected $table = 'usuarios';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'email',
        'password_hash',
        'nombre_completo',
        'telefono',
        'foto_url',
        'rol',
        'email_verified_at',
        'remember_token',
        'is_active',
        'ultimo_acceso',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_acceso' => 'datetime',
        'is_active' => 'boolean',
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

    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = bcrypt($value);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('rol', $role);
    }

    public function isAdmin(): bool
    {
        return in_array($this->rol, ['admin', 'superadmin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->rol === 'superadmin';
    }
}