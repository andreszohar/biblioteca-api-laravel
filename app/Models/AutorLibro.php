<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AutorLibro extends Pivot
{
    protected $table = 'autor_libro';

    // La tabla tiene su propio id autoincremental
    public $incrementing = true;

    protected $fillable = [
        'autor_id',
        'libro_id',
        'orden_autor',
    ];

    protected function casts(): array
    {
        return [
            'orden_autor' => 'integer',
        ];
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Autor::class);
    }

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class);
    }
}