<?php

namespace App\Console\Commands;

use App\Models\Libro;
use App\Models\Prestamo;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('biblioteca:reporte')]
#[Description('Reporte: libros más prestados, usuarios con préstamos vencidos y libros sin stock')]
class ReporteBiblioteca extends Command
{
    public function handle(): int
    {
        $this->info('LIBROS MÁS PRESTADOS (top 5)');
        $masPrestados = Libro::withCount('prestamos')
            ->has('prestamos')
            ->orderByDesc('prestamos_count')
            ->limit(5)
            ->get()
            ->map(fn ($libro) => [$libro->id, $libro->titulo, $libro->prestamos_count]);
        $this->table(['ID', 'Título', 'Préstamos'], $masPrestados);

        $this->newLine();
        $this->info('USUARIOS CON PRÉSTAMOS VENCIDOS');
        $vencidos = Prestamo::with(['user', 'libro'])
            ->where(function ($q) {
                $q->where('estado', Prestamo::ESTADO_VENCIDO)
                    ->orWhere(function ($q2) {
                        $q2->where('estado', Prestamo::ESTADO_ACTIVO)
                            ->whereDate('fecha_devolucion_estimada', '<', now()->toDateString());
                    });
            })
            ->get()
            ->map(fn ($p) => [
                $p->user->nombre,
                $p->user->email,
                $p->libro->titulo,
                $p->fecha_devolucion_estimada->toDateString(),
            ]);
        $this->table(['Usuario', 'Correo', 'Libro', 'Debía devolver'], $vencidos);

        $this->newLine();
        $this->info('LIBROS SIN STOCK');
        $sinStock = Libro::where('stock_disponible', 0)
            ->get()
            ->map(fn ($libro) => [$libro->id, $libro->titulo, $libro->isbn]);
        $this->table(['ID', 'Título', 'ISBN'], $sinStock);

        return self::SUCCESS;
    }
}