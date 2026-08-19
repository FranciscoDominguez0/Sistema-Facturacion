<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vendedor>
 */
class VendedorFactory extends Factory
{
    protected $model = Vendedor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'codigo' => fake()->optional()->bothify('VEN-####'),
            'comision_porcentaje' => fake()->optional()->randomFloat(2, 0, 100),
            'descuento_maximo_porcentaje' => 0,
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}
