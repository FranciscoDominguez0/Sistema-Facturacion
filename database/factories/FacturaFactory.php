<?php

namespace Database\Factories;

use App\Enums\EstadoFactura;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\User;
use App\Services\FacturaService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Factura>
 */
class FacturaFactory extends Factory
{
    protected $model = Factura::class;

    public function definition(): array
    {
        return [
            'numero_factura' => 'FAC-'.str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'cliente_id' => Cliente::factory(),
            'vendedor_id' => User::factory(),
            'fecha_emision' => now(),
            'fecha_vencimiento' => null,
            'subtotal' => 0,
            'descuento_porcentaje' => 0,
            'descuento_total' => 0,
            'impuesto' => 0,
            'total' => 0,
            'estado' => EstadoFactura::PENDIENTE,
            'notas' => null,
        ];
    }

    public function pagada(): static
    {
        return $this->state(fn () => ['estado' => EstadoFactura::PAGADA]);
    }

    public function anulada(): static
    {
        return $this->state(fn () => ['estado' => EstadoFactura::ANULADA]);
    }

    /**
     * Crea líneas de venta reales y recalcula los totales con el servicio.
     */
    public function conItems(int $cantidad = 2): static
    {
        return $this->afterCreating(function (Factura $factura) use ($cantidad) {
            $empresa = Empresa::first() ?? Empresa::factory()->create();

            $items = [];

            for ($i = 0; $i < $cantidad; $i++) {
                $items[] = [
                    'descripcion' => fake()->words(3, true),
                    'cantidad' => fake()->numberBetween(1, 5),
                    'precio_unitario' => fake()->randomFloat(2, 10, 500),
                    'descuento_porcentaje' => 0,
                    'impuesto_nombre' => $empresa->impuesto_nombre,
                    'impuesto_porcentaje' => $empresa->impuesto_porcentaje,
                ];
            }

            $totales = app(FacturaService::class)->calcularTotales($items, 0);

            foreach ($totales['items_actualizados'] as $item) {
                FacturaItem::create([
                    'factura_id' => $factura->id,
                    'producto_id' => null,
                    'descripcion' => $item['descripcion'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'descuento_porcentaje' => $item['descuento_porcentaje'] ?? 0,
                    'descuento_monto' => $item['descuento_monto'],
                    'subtotal_linea' => $item['subtotal_linea'],
                    'impuesto_nombre' => $item['impuesto_nombre'],
                    'impuesto_porcentaje' => $item['impuesto_porcentaje'],
                    'impuesto_monto' => $item['impuesto_monto'],
                ]);
            }

            $factura->update([
                'subtotal' => $totales['subtotal'],
                'descuento_total' => $totales['descuento_total'],
                'impuesto' => $totales['impuesto'],
                'total' => $totales['total'],
            ]);
        });
    }
}
