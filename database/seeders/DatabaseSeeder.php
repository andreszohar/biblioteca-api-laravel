<?php

namespace Database\Seeders;

use App\Models\Autor;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 10 autores
        $autores = Autor::factory()->count(10)->create();

        // 20 libros: 17 con stock y 3 sin stock
        $conStock = Libro::factory()
            ->count(17)
            ->state(fn () => ['stock_disponible' => random_int(2, 12)])
            ->create();
        $sinStock = Libro::factory()->count(3)->sinStock()->create();
        $libros = $conStock->concat($sinStock)->values();

        // Cada libro recibe de 1 a 3 autores, con su orden_autor
        foreach ($libros as $libro) {
            $ids = $autores->random(random_int(1, 3))->pluck('id')->values();

            foreach ($ids as $posicion => $autorId) {
                $libro->autores()->attach($autorId, ['orden_autor' => $posicion + 1]);
            }
        }

        // 15 usuarios: 1 administrador conocido y 14 aleatorios
        $admin = User::factory()->create([
            'nombre' => 'Administrador',
            'email' => 'admin@biblioteca.test',
        ]);
        $usuarios = collect([$admin])
            ->concat(User::factory()->count(14)->create())
            ->values();

        // 10 préstamos: 6 activos, 2 vencidos y 2 devueltos
        $disponibles = $libros->where('stock_disponible', '>', 0)->values();
        $plan = ['activo', 'activo', 'activo', 'activo', 'activo', 'activo', 'vencido', 'vencido', 'devuelto', 'devuelto'];

        foreach ($plan as $i => $estado) {
            $factory = Prestamo::factory();

            if ($estado === 'vencido') {
                $factory = $factory->vencido();
            }
            if ($estado === 'devuelto') {
                $factory = $factory->devuelto();
            }

            $libro = $disponibles[$i];

            $factory->create([
                'libro_id' => $libro->id,
                'user_id' => $usuarios[$i]->id,
            ]);

            // Un libro prestado (activo o vencido) resta una unidad del stock
            if ($estado !== 'devuelto') {
                $libro->decrement('stock_disponible');
            }
        }
    }
}