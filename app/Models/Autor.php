<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Autor extends Model
{
    use HasFactory;

    protected $table = 'autores';

    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'nacionalidad',
        'biografia',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function libros(): BelongsToMany
    {
        return $this->belongsToMany(Libro::class, 'autor_libro', 'autor_id', 'libro_id')
            ->using(AutorLibro::class)
            ->withPivot('orden_autor')
            ->withTimestamps();
    }

    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(fn () => "{$this->nombre} {$this->apellido}");
    }
}