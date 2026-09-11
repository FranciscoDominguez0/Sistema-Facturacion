<?php

namespace App\Livewire\Forms;

use App\Services\FacturaService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Form;

class FacturaForm extends Form
{
    #[Url]
    public $cliente_id;

    public $vendedor_id;

    public $fecha_emision;

    public $fecha_vencimiento;

    public $descuento_porcentaje = 0;

    public $notas;

    public array $items = [];

    // Totales
    public $subtotal = 0;

    public $descuento_total = 0;

    public $impuesto = 0;

    public $total = 0;

    public array $desglose_impuestos = [];

    public function init()
    {
        if (! $this->fecha_emision) {
            $this->fecha_emision = date('Y-m-d');
        }
        if (empty($this->items)) {
            $this->agregarLinea();
        }
    }

    public function agregarLinea()
    {
        $this->items[] = [
            'producto_id' => null,
            'descripcion' => '',
            'cantidad' => 1,
            'precio_unitario' => 0,
            'descuento_porcentaje' => 0,
            'descuento_monto' => 0,
            'subtotal_linea' => 0,
            'impuesto_id' => null,
            'impuesto_nombre' => null,
            'impuesto_porcentaje' => 0,
            'impuesto_monto' => 0,
        ];
    }

    public function eliminarLinea($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recalcularTotales();
    }

    public function recalcularTotales()
    {
        $facturaService = app(FacturaService::class);

        $resultado = $facturaService->calcularTotales(
            $this->items,
            floatval($this->descuento_porcentaje ?: 0)
        );

        $this->subtotal = $resultado['subtotal'];
        $this->descuento_total = $resultado['descuento_total'];
        $this->impuesto = $resultado['impuesto'];
        $this->desglose_impuestos = $resultado['desglose_impuestos'] ?? [];
        $this->total = $resultado['total'];
        $this->items = $resultado['items_actualizados'];
    }

    public function rules(): array
    {
        $reglas = [
            'cliente_id' => 'required|exists:clientes,id',
            'vendedor_id' => 'required|exists:users,id',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.descripcion' => 'nullable|string',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ];

        return $reglas;
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'vendedor_id.required' => 'Debe seleccionar un vendedor.',
            'items.min' => 'La factura debe tener al menos una línea.',
            'items.*.cantidad.min' => 'La cantidad debe ser un entero mayor a 0.',
            'items.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'items.*.descuento_porcentaje.max' => 'El descuento no puede superar el 100%.',
        ];
    }

    public function guardar()
    {
        // Descuento por línea quitado del formulario: siempre se fuerza a 0
        foreach ($this->items as &$item) {
            $item['descuento_porcentaje'] = 0;
        }
        unset($item);

        if (! Gate::allows('facturas.descuento')) {
            $this->descuento_porcentaje = 0;
        }

        $this->validate();

        $facturaService = app(FacturaService::class);

        return $facturaService->crear($this->all());
    }
}
