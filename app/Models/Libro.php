<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Libro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'libros';

    protected $fillable = [
        'titulo',
        'isbn',
        'anio_publicacion',
        'numero_paginas',
        'descripcion',
        'stock_disponible',
    ];

    protected function casts(): array
    {
        return [
            'anio_publicacion' => 'integer',
            'numero_paginas' => 'integer',
            'stock_disponible' => 'integer',
        ];
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_libro', 'libro_id', 'autor_id')
            ->using(AutorLibro::class)
            ->withPivot('orden_autor')
            ->withTimestamps()
            ->orderByPivot('orden_autor');
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('stock_disponible', '>', 0);
    }

    public function scopePorAnio(Builder $query, int $anio): Builder
    {
        return $query->where('anio_publicacion', $anio);
    }

    public function scopePorAutor(Builder $query, int $autorId): Builder
    {
        return $query->whereHas('autores', fn (Builder $q) => $q->where('autores.id', $autorId));
    }

    protected function titulo(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => trim($value),
        );
    }

    protected function estaDisponible(): Attribute
    {
        return Attribute::get(fn () => $this->stock_disponible > 0);
    }
}