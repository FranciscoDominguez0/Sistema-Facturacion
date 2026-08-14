<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->words(2, true),
            'descripcion' => fake()->sentence(),
            'codigo' => fake()->unique()->bothify('SKU-####'),
            'precio' => fake()->randomFloat(2, 1, 1000),
            'tipo' => 'producto',
            'aplica_impuesto' => true,
            'imagen_path' => null,
            'activo' => true,
        ];
    }

    public function servicio(): static
    {
        return $this->state(fn () => ['tipo' => 'servicio']);
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}
