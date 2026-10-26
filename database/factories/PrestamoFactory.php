<?php

namespace Database\Factories;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prestamo>
 */
class PrestamoFactory extends Factory
{
    public function definition(): array
    {
        $fechaPrestamo = Carbon::now()->subDays(fake()->numberBetween(0, 10));

        return [
            'libro_id' => Libro::factory(),
            'user_id' => User::factory(),
            'fecha_prestamo' => $fechaPrestamo->toDateString(),
            'fecha_devolucion_estimada' => $fechaPrestamo->copy()->addDays(15)->toDateString(),
            'fecha_devolucion_real' => null,
            'estado' => Prestamo::ESTADO_ACTIVO,
        ];
    }

    public function devuelto(): static
    {
        return $this->state(function () {
            $fechaPrestamo = Carbon::now()->subDays(fake()->numberBetween(20, 40));

            return [
                'fecha_prestamo' => $fechaPrestamo->toDateString(),
                'fecha_devolucion_estimada' => $fechaPrestamo->copy()->addDays(15)->toDateString(),
                'fecha_devolucion_real' => $fechaPrestamo->copy()->addDays(fake()->numberBetween(3, 14))->toDateString(),
                'estado' => Prestamo::ESTADO_DEVUELTO,
            ];
        });
    }

    public function vencido(): static
    {
        return $this->state(function () {
            $fechaPrestamo = Carbon::now()->subDays(fake()->numberBetween(20, 40));

            return [
                'fecha_prestamo' => $fechaPrestamo->toDateString(),
                'fecha_devolucion_estimada' => $fechaPrestamo->copy()->addDays(15)->toDateString(),
                'fecha_devolucion_real' => null,
                'estado' => Prestamo::ESTADO_VENCIDO,
            ];
        });
    }
}