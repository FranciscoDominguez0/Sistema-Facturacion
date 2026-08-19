<?php

namespace App\Services;

use App\Enums\EstadoFactura;
use App\Models\Empresa;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;
use Exception;

class FacturaService
{
    /**
     * Genera un número correlativo seguro para la factura.
     */
    public function generarNumero(): string
    {
        return DB::transaction(function () {
            // Se asume que existe un solo registro de empresa
            $empresa = Empresa::lockForUpdate()->first();
            
            if (!$empresa) {
                // Crear empresa por defecto para mantener la secuencia
                $empresa = Empresa::create([
                    'nombre' => 'Mi Empresa (No Configurada)',
                    'identificacion_fiscal' => '000000000',
                    'moneda' => 'USD',
                    'simbolo_moneda' => '$',
                    'impuesto_nombre' => 'ITBMS',
                    'impuesto_porcentaje' => 7,
                    'prefijo_factura' => 'FAC-',
                    'siguiente_numero_factura' => 1,
                    'color_primario' => '#000000'
                ]);
            }
            
            $numero = $empresa->siguiente_numero_factura;
            $prefijo = $empresa->prefijo_factura;
            
            $empresa->siguiente_numero_factura = $numero + 1;
            $empresa->save();
            
            $numeroFormateado = $prefijo . str_pad((string)$numero, 6, '0', STR_PAD_LEFT);
            
            return $numeroFormateado;
        });
    }

    /**
     * Calcula los totales de la factura.
     */
    public function calcularTotales(array $items, float $impuestoPorcentaje, float $descuentoPorcentaje = 0): array
    {
        $subtotal = 0;
        $subtotalGravable = 0;
        
        foreach ($items as &$item) {
            $cantidad = floatval($item['cantidad'] ?? 0);
            $precioUnitario = floatval($item['precio_unitario'] ?? 0);
            $descuentoLineaPorcentaje = floatval($item['descuento_porcentaje'] ?? 0);
            $aplicaImpuesto = boolval($item['aplica_impuesto'] ?? true);
            
            $subtotalBrutoLinea = $cantidad * $precioUnitario;
            $descuentoMontoLinea = $subtotalBrutoLinea * ($descuentoLineaPorcentaje / 100);
            $subtotalLinea = $subtotalBrutoLinea - $descuentoMontoLinea;
            
            $item['descuento_monto'] = $descuentoMontoLinea;
            $item['subtotal_linea'] = $subtotalLinea;
            
            $subtotal += $subtotalLinea;
            if ($aplicaImpuesto) {
                $subtotalGravable += $subtotalLinea;
            }
        }
        
        $descuentoTotalGlobal = $subtotal * ($descuentoPorcentaje / 100);
        $subtotalConDescuento = $subtotal - $descuentoTotalGlobal;
        
        // El impuesto solo se calcula sobre la parte gravable, descontando la porción del descuento global si aplica.
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
     * Crea la factura y sus items.
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
            
            foreach ($totales['items_actualizados'] as $itemData) {
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
            
            return $factura;
        });
    }

    public function cambiarEstado(Factura $factura, EstadoFactura $nuevoEstado): Factura
    {
        $factura->update([
            'estado' => $nuevoEstado,
        ]);
        
        return $factura;
    }
}
