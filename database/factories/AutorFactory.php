<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Autor>
 */
class AutorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-90 years', '-25 years')->format('Y-m-d'),
            'nacionalidad' => fake()->randomElement([
                'Colombiana',
                'Argentina',
                'Mexicana',
                'Chilena',
                'Peruana',
                'Española',
                'Uruguaya',
                'Venezolana',
            ]),
            'biografia' => fake()->paragraph(3),
        ];
    }
}