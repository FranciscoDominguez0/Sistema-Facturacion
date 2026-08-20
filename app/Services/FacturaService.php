<?php

namespace App\Services;

use App\Enums\EstadoFactura;
use App\Models\Empresa;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;

class FacturaService
{
    /**
     * Genera un número correlativo para la nueva factura (ej: FAC-000001).
     */
    public function generarNumero(): string
    {
        return DB::transaction(function () {
            $empresa = $this->obtenerOCrearEmpresaPorDefecto();

            $numero = $empresa->siguiente_numero_factura;
            $prefijo = $empresa->prefijo_factura;

            $numeroGenerado = $prefijo.str_pad((string) $numero, 6, '0', STR_PAD_LEFT);
            while (Factura::where('numero_factura', $numeroGenerado)->exists()) {
                $numero++;
                $numeroGenerado = $prefijo.str_pad((string) $numero, 6, '0', STR_PAD_LEFT);
            }

            $empresa->siguiente_numero_factura = $numero + 1;
            $empresa->save();

            return $numeroGenerado;
        });
    }

    /**
     * Calcula el subtotal, descuentos, impuestos y total de toda la factura.
     */
    public function calcularTotales(array $items, float $impuestoPorcentaje, float $descuentoPorcentaje = 0): array
    {
        $subtotal = 0;
        $subtotalGravable = 0;

        foreach ($items as &$item) {
            $resultadoLinea = $this->calcularLinea($item);

            $item['descuento_monto'] = $resultadoLinea['descuento_monto'];
            $item['subtotal_linea'] = $resultadoLinea['subtotal_linea'];

            $subtotal += $resultadoLinea['subtotal_linea'];
            if ($resultadoLinea['aplica_impuesto']) {
                $subtotalGravable += $resultadoLinea['subtotal_linea'];
            }
        }

        $descuentoTotalGlobal = $subtotal * ($descuentoPorcentaje / 100);
        $subtotalConDescuento = $subtotal - $descuentoTotalGlobal;

        // El impuesto solo aplica sobre lo gravable, y se descuenta su proporción del descuento global.
        $proporcionGravable = $subtotal > 0 ? ($subtotalGravable / $subtotal) : 0;
        $subtotalGravableConDescuento = $subtotalGravable - ($descuentoTotalGlobal * $proporcionGravable);

        $impuestoMonto = $subtotalGravableConDescuento * ($impuestoPorcentaje / 100);
        $total = $subtotalConDescuento + $impuestoMonto;

        return [
            'subtotal' => round($subtotal, 2),
            'descuento_total' => round($descuentoTotalGlobal, 2),
            'impuesto' => round($impuestoMonto, 2),
            'total' => round($total, 2),
            'items_actualizados' => $items,
        ];
    }

    /**
     * Crea una nueva factura con todas sus líneas de detalle en la base de datos.
     */
    public function crear(array $data): Factura
    {
        return DB::transaction(function () use ($data) {
            $empresa = Empresa::first();
            $impuestoPorcentaje = $empresa ? floatval($empresa->impuesto_porcentaje) : 0;

            $numeroFactura = $this->generarNumero();
            $totales = $this->calcularTotales($data['items'], $impuestoPorcentaje, floatval($data['descuento_porcentaje'] ?? 0));

            $factura = Factura::create([
                'numero_factura' => $numeroFactura,
                'cliente_id' => $data['cliente_id'],
                'vendedor_id' => $data['vendedor_id'],
                'fecha_emision' => $data['fecha_emision'],
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
                'subtotal' => $totales['subtotal'],
                'descuento_porcentaje' => floatval($data['descuento_porcentaje'] ?? 0),
                'descuento_total' => $totales['descuento_total'],
                'impuesto' => $totales['impuesto'],
                'total' => $totales['total'],
                'estado' => EstadoFactura::PENDIENTE,
                'notas' => $data['notas'] ?? null,
            ]);

            $this->guardarLineas($factura, $totales['items_actualizados']);

            return $factura;
        });
    }

    /**
     * Actualiza una factura, reemplazando todas sus líneas y recalculando totales.
     */
    public function actualizar(Factura $factura, array $data): Factura
    {
        return DB::transaction(function () use ($factura, $data) {
            $empresa = Empresa::first();
            $impuestoPorcentaje = $empresa ? floatval($empresa->impuesto_porcentaje) : 0;

            $totales = $this->calcularTotales($data['items'], $impuestoPorcentaje, floatval($data['descuento_porcentaje'] ?? 0));

            $factura->update([
                'cliente_id' => $data['cliente_id'] ?? $factura->cliente_id,
                'vendedor_id' => $data['vendedor_id'] ?? $factura->vendedor_id,
                'fecha_emision' => $data['fecha_emision'] ?? $factura->fecha_emision,
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? $factura->fecha_vencimiento,
                'descuento_porcentaje' => floatval($data['descuento_porcentaje'] ?? 0),
                'subtotal' => $totales['subtotal'],
                'descuento_total' => $totales['descuento_total'],
                'impuesto' => $totales['impuesto'],
                'total' => $totales['total'],
                'notas' => $data['notas'] ?? $factura->notas,
            ]);

            $factura->items()->delete();
            $this->guardarLineas($factura, $totales['items_actualizados']);

            return $factura;
        });
    }

    /**
     * Marca una factura como anulada.
     */
    public function anular(Factura $factura): Factura
    {
        if ($factura->estado === EstadoFactura::ANULADA) {
            throw new \DomainException('La factura ya está anulada y no se puede anular de nuevo.');
        }

        return $this->cambiarEstado($factura, EstadoFactura::ANULADA);
    }

    /**
     * Transiciones de estado permitidas para el flujo de la factura.
     */
    private const TRANSICIONES_PERMITIDAS = [
        'Pendiente' => [EstadoFactura::PAGADA, EstadoFactura::ANULADA],
        'Pagada' => [EstadoFactura::PENDIENTE, EstadoFactura::ANULADA],
        'Anulada' => [],
    ];

    /**
     * Cambia el estado de una factura verificando que el cambio tenga sentido.
     */
    public function cambiarEstado(Factura $factura, EstadoFactura $nuevoEstado): Factura
    {
        $estadoActual = $factura->estado instanceof EstadoFactura ? $factura->estado->value : $factura->estado;
        $permitidas = self::TRANSICIONES_PERMITIDAS[$estadoActual] ?? [];

        if (! in_array($nuevoEstado, $permitidas, true)) {
            throw new \DomainException(
                sprintf("No se puede cambiar la factura %s de estado '%s' a '%s'.",
                    $factura->numero_factura,
                    $factura->estado->value,
                    $nuevoEstado->value
                )
            );
        }

        $factura->update([
            'estado' => $nuevoEstado,
        ]);

        return $factura;
    }

    /**
     * Obtiene la empresa y bloquea la fila para evitar que dos facturas generen el mismo número simultáneamente.
     */
    private function obtenerOCrearEmpresaPorDefecto(): Empresa
    {
        $empresa = Empresa::lockForUpdate()->first();

        if (! $empresa) {
            $empresa = Empresa::create([
                'nombre' => 'Mi Empresa (No Configurada)',
                'identificacion_fiscal' => '000000000',
                'moneda' => 'USD',
                'simbolo_moneda' => '$',
                'impuesto_nombre' => 'ITBMS',
                'impuesto_porcentaje' => 7,
                'prefijo_factura' => 'FAC-',
                'siguiente_numero_factura' => 1,
                'color_primario' => '#000000',
            ]);
        }

        return $empresa;
    }

    /**
     * Calcula el monto bruto, descuentos e impuestos para una sola línea (producto/servicio).
     */
    private function calcularLinea(array $item): array
    {
        $cantidad = floatval($item['cantidad'] ?? 0);
        $precioUnitario = floatval($item['precio_unitario'] ?? 0);
        $descuentoLineaPorcentaje = floatval($item['descuento_porcentaje'] ?? 0);
        $aplicaImpuesto = boolval($item['aplica_impuesto'] ?? true);

        $subtotalBrutoLinea = $cantidad * $precioUnitario;
        $descuentoMontoLinea = $subtotalBrutoLinea * ($descuentoLineaPorcentaje / 100);
        $subtotalLinea = $subtotalBrutoLinea - $descuentoMontoLinea;

        return [
            'descuento_monto' => $descuentoMontoLinea,
            'subtotal_linea' => $subtotalLinea,
            'aplica_impuesto' => $aplicaImpuesto,
        ];
    }

    /**
     * Guarda las líneas (ítems) calculadas en la base de datos vinculadas a una factura.
     */
    private function guardarLineas(Factura $factura, array $items): void
    {
        foreach ($items as $itemData) {
            $factura->items()->create([
                'producto_id' => $itemData['producto_id'] ?? null,
                'descripcion' => $itemData['descripcion'],
                'cantidad' => $itemData['cantidad'],
                'precio_unitario' => $itemData['precio_unitario'],
                'descuento_porcentaje' => floatval($itemData['descuento_porcentaje'] ?? 0),
                'descuento_monto' => $itemData['descuento_monto'],
                'subtotal_linea' => $itemData['subtotal_linea'],
            ]);
        }
    }
}
