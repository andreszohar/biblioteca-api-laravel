<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestamo extends Model
{
    use HasFactory;

    public const ESTADO_ACTIVO = 'activo';
    public const ESTADO_DEVUELTO = 'devuelto';
    public const ESTADO_VENCIDO = 'vencido';

    protected $table = 'prestamos';

    protected $fillable = [
        'libro_id',
        'user_id',
        'fecha_prestamo',
        'fecha_devolucion_estimada',
        'fecha_devolucion_real',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_prestamo' => 'date',
            'fecha_devolucion_estimada' => 'date',
            'fecha_devolucion_real' => 'date',
        ];
    }

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Préstamos que todavía no se han devuelto (activos o vencidos)
    public function scopeSinDevolver(Builder $query): Builder
    {
        return $query->whereIn('estado', [self::ESTADO_ACTIVO, self::ESTADO_VENCIDO]);
    }

    // Activos cuya fecha estimada ya pasó (candidatos a marcarse como vencidos)
    public function scopeAtrasados(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_ACTIVO)
            ->whereDate('fecha_devolucion_estimada', '<', now()->toDateString());
    }
}