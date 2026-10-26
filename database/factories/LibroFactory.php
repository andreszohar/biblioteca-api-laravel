<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Libro>
 */
class LibroFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titulo' => ucfirst(fake()->words(3, true)),
            'isbn' => fake()->unique()->isbn13(),
            'anio_publicacion' => fake()->numberBetween(1950, 2025),
            'numero_paginas' => fake()->numberBetween(80, 900),
            'descripcion' => fake()->paragraph(2),
            'stock_disponible' => fake()->numberBetween(0, 12),
        ];
    }

    public function sinStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_disponible' => 0,
        ]);
    }
}