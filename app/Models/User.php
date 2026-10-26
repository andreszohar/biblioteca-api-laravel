<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ESTADO_ACTIVO = 'activo';
    public const ESTADO_INACTIVO = 'inactivo';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'fecha_registro',
        'estado',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'fecha_registro' => 'date',
            'password' => 'hashed',
        ];
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }
}