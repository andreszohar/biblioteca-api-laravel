<?php

namespace App\Services;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PrestamoService
{
    public const MAX_PRESTAMOS_ACTIVOS = 3;
    public const DIAS_PRESTAMO = 15;

    public function crear(User $user, Libro $libro): Prestamo
    {
        return DB::transaction(function () use ($user, $libro) {
            // Bloquea la fila del libro: evita que dos préstamos simultáneos se lleven el último ejemplar
            $libro = Libro::whereKey($libro->id)->lockForUpdate()->firstOrFail();

            if ($user->estado !== User::ESTADO_ACTIVO) {
                throw ValidationException::withMessages([
                    'user_id' => ['El usuario está inactivo y no puede pedir préstamos.'],
                ]);
            }

            if ($libro->stock_disponible <= 0) {
                throw ValidationException::withMessages([
                    'libro_id' => ['No hay stock disponible para este libro.'],
                ]);
            }

            if ($user->prestamos()->sinDevolver()->count() >= self::MAX_PRESTAMOS_ACTIVOS) {
                throw ValidationException::withMessages([
                    'user_id' => ['El usuario ya tiene ' . self::MAX_PRESTAMOS_ACTIVOS . ' préstamos activos.'],
                ]);
            }

            $libro->decrement('stock_disponible');

            return Prestamo::create([
                'libro_id' => $libro->id,
                'user_id' => $user->id,
                'fecha_prestamo' => now()->toDateString(),
                'fecha_devolucion_estimada' => now()->addDays(self::DIAS_PRESTAMO)->toDateString(),
                'estado' => Prestamo::ESTADO_ACTIVO,
            ]);
        });
    }

    public function devolver(Prestamo $prestamo): Prestamo
    {
        return DB::transaction(function () use ($prestamo) {
            $prestamo = Prestamo::whereKey($prestamo->id)->lockForUpdate()->firstOrFail();

            if ($prestamo->estado === Prestamo::ESTADO_DEVUELTO) {
                throw ValidationException::withMessages([
                    'prestamo' => ['Este préstamo ya fue devuelto.'],
                ]);
            }

            $prestamo->update([
                'fecha_devolucion_real' => now()->toDateString(),
                'estado' => Prestamo::ESTADO_DEVUELTO,
            ]);

            Libro::withTrashed()->whereKey($prestamo->libro_id)->increment('stock_disponible');

            return $prestamo;
        });
    }

    public function marcarVencidos(): int
    {
        return Prestamo::atrasados()->update(['estado' => Prestamo::ESTADO_VENCIDO]);
    }
}