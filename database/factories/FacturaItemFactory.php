<?php

namespace Database\Factories;

use App\Models\Factura;
use App\Models\FacturaItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacturaItem>
 */
class FacturaItemFactory extends Factory
{
    protected $model = FacturaItem::class;

    public function definition(): array
    {
        return [
            'factura_id' => Factura::factory(),
            'producto_id' => null,
            'descripcion' => fake()->words(3, true),
            'cantidad' => 1,
            'precio_unitario' => fake()->randomFloat(2, 5, 500),
            'descuento_porcentaje' => 0,
            'descuento_monto' => 0,
            'subtotal_linea' => 0,
        ];
    }
}
