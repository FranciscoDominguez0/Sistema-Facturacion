<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Empresa>
 */
class EmpresaFactory extends Factory
{
    protected $model = Empresa::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'identificacion_fiscal' => fake()->numerify('############'),
            'email' => null,
            'telefono' => null,
            'ruc' => null,
            'dv' => null,
            'logo_path' => null,
            'moneda' => 'USD',
            'simbolo_moneda' => '$',
            'impuesto_nombre' => 'ITBMS',
            'impuesto_porcentaje' => 7,
            'prefijo_factura' => 'FAC-',
            'siguiente_numero_factura' => 1,
            'color_primario' => '#000000',
            'pie_pagina_pdf' => null,
        ];
    }
}
