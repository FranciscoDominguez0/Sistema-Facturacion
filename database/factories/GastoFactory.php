<?php

namespace Database\Factories;

use App\Models\Gasto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GastoFactory extends Factory
{
    protected $model = Gasto::class;

    public function definition(): array
    {
        $categorias = config('gastos.categorias');

        return [
            'concepto' => fake()->sentence(3),
            'categoria' => $categorias[array_rand($categorias)],
            'monto' => fake()->randomFloat(2, 10, 1000),
            'fecha' => fake()->date(),
            'registrado_por' => User::factory(),
            'comprobante' => fake()->boolean() ? fake()->numerify('FAC-####') : null,
        ];
    }
}
