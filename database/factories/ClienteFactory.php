<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'identificacion' => fake()->optional()->numerify('###-####-#####'),
            'email' => fake()->optional()->safeEmail(),
            'telefono' => fake()->optional()->phoneNumber(),
            'direccion' => fake()->optional()->address(),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}
