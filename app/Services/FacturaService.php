<?php

namespace App\Services;

use App\Enums\EstadoFactura;
use App\Mail\FacturaMail;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
    public function calcularTotales(array $items, float $descuentoPorcentaje = 0): array
    {
        $subtotal = 0;
        $impuestoTotal = 0;
        $desgloseImpuestos = [];

        foreach ($items as &$item) {
            $resultadoLinea = $this->calcularLinea($item);

            $item['descuento_monto'] = $resultadoLinea['descuento_monto'];
            $item['subtotal_linea'] = $resultadoLinea['subtotal_linea'];
            $item['impuesto_monto'] = $resultadoLinea['impuesto_monto'];

            $subtotal += $resultadoLinea['subtotal_linea'];
            $impuestoTotal += $resultadoLinea['impuesto_monto'];

            // Desglose
            $impuestoPorcentajeLinea = floatval($item['impuesto_porcentaje'] ?? 0);

            if ($impuestoPorcentajeLinea > 0) {
                $nombre = $item['impuesto_nombre'] ?? 'Impuesto';
                $porc = number_format($impuestoPorcentajeLinea, 2).'%';
                $llave = "$nombre ($porc)";

                if (! isset($desgloseImpuestos[$llave])) {
                    $desgloseImpuestos[$llave] = 0;
                }
                $desgloseImpuestos[$llave] += $resultadoLinea['impuesto_monto'];
            }
        }

        $descuentoTotalGlobal = $subtotal * ($descuentoPorcentaje / 100);
        $subtotalConDescuento = $subtotal - $descuentoTotalGlobal;

        // El descuento global se aplica proporcionalmente al impuesto de cada línea.
        $factorDescuento = $subtotal > 0 ? ($subtotalConDescuento / $subtotal) : 1;
        $impuestoTotalAjustado = $impuestoTotal * $factorDescuento;

        $desgloseAjustado = [];
        foreach ($desgloseImpuestos as $llave => $monto) {
            $desgloseAjustado[$llave] = round($monto * $factorDescuento, 2);
        }

        $total = $subtotalConDescuento + $impuestoTotalAjustado;

        return [
            'subtotal' => round($subtotal, 2),
            'descuento_total' => round($descuentoTotalGlobal, 2),
            'impuesto' => round($impuestoTotalAjustado, 2),
            'desglose_impuestos' => $desgloseAjustado,
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
            $numeroFactura = $this->generarNumero();
            $totales = $this->calcularTotales($data['items'], floatval($data['descuento_porcentaje'] ?? 0));

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

            // Asegura que el id y los timestamps generados por la BD estén cargados
            $factura->refresh();

            return $factura;
        });
    }

    /**
     * Actualiza una factura, reemplazando todas sus líneas y recalculando totales.
     */
    public function actualizar(Factura $factura, array $data): Factura
    {
        return DB::transaction(function () use ($factura, $data) {
            $totales = $this->calcularTotales($data['items'], floatval($data['descuento_porcentaje'] ?? 0));

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
     * Envía la factura en PDF al correo del cliente.
     * Devuelve false si el cliente no tiene correo registrado.
     */
    public function enviarPorCorreo(Factura $factura): bool
    {
        $factura->loadMissing('cliente');

        if (! $factura->cliente->email) {
            return false;
        }

        Mail::to($factura->cliente->email)->send(new FacturaMail($factura));

        return true;
    }

    /**
     * Elimina una factura y todas sus líneas de detalle.
     */
    public function eliminar(Factura $factura): void
    {
        DB::transaction(function () use ($factura) {
            $factura->items()->delete();
            $factura->delete();
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

        $impuestoPorcentaje = floatval($item['impuesto_porcentaje'] ?? 0);

        $subtotalBrutoLinea = $cantidad * $precioUnitario;
        $descuentoMontoLinea = $subtotalBrutoLinea * ($descuentoLineaPorcentaje / 100);
        $subtotalLinea = $subtotalBrutoLinea - $descuentoMontoLinea;

        $impuestoMonto = $subtotalLinea * ($impuestoPorcentaje / 100);

        return [
            'descuento_monto' => $descuentoMontoLinea,
            'subtotal_linea' => $subtotalLinea,
            'impuesto_monto' => $impuestoMonto,
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
                'descripcion' => $this->resolverDescripcion($itemData),
                'cantidad' => $itemData['cantidad'],
                'precio_unitario' => $itemData['precio_unitario'],
                'descuento_porcentaje' => floatval($itemData['descuento_porcentaje'] ?? 0),
                'descuento_monto' => $itemData['descuento_monto'],
                'subtotal_linea' => $itemData['subtotal_linea'],
                'impuesto_id' => $itemData['impuesto_id'] ?? null,
                'impuesto_nombre' => $itemData['impuesto_nombre'] ?? null,
                'impuesto_porcentaje' => floatval($itemData['impuesto_porcentaje'] ?? 0),
                'impuesto_monto' => floatval($itemData['impuesto_monto'] ?? 0),
            ]);
        }
    }

    /**
     * Si la línea quedó sin descripción escrita y tiene producto,
     * se usa la del producto para que la línea no quede en blanco.
     */
    private function resolverDescripcion(array $itemData): string
    {
        $descripcion = trim((string) ($itemData['descripcion'] ?? ''));

        if ($descripcion !== '') {
            return $descripcion;
        }

        $producto = Producto::find($itemData['producto_id'] ?? null);

        return $producto ? ($producto->descripcion ?: $producto->nombre) : '';
    }
}
