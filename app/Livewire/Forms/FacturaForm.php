<?php

namespace App\Livewire\Forms;

use App\Models\Empresa;
use App\Models\Vendedor;
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
            'aplica_impuesto' => true,
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
        $empresa = Empresa::first();
        $impuestoPorcentaje = $empresa ? floatval($empresa->impuesto_porcentaje) : 0;

        $resultado = $facturaService->calcularTotales(
            $this->items,
            $impuestoPorcentaje,
            floatval($this->descuento_porcentaje ?: 0)
        );

        $this->subtotal = $resultado['subtotal'];
        $this->descuento_total = $resultado['descuento_total'];
        $this->impuesto = $resultado['impuesto'];
        $this->total = $resultado['total'];
        $this->items = $resultado['items_actualizados'];
    }

    public function rules(): array
    {
        $reglas = [
            'cliente_id' => 'required|exists:clientes,id',
            'vendedor_id' => 'required|exists:vendedores,id',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.descripcion' => 'required|string',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ];

        // Validar descuentos si el usuario tiene permisos
        $puedeDescontar = Gate::allows('facturas.descuento');
        if ($puedeDescontar && $this->vendedor_id) {
            $descuentoMaximo = (float) (Vendedor::find($this->vendedor_id)?->descuento_maximo_porcentaje ?? 0);
            $reglas['items.*.descuento_porcentaje'] = ['required', 'numeric', 'min:0', "max:{$descuentoMaximo}"];
        }

        return $reglas;
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'vendedor_id.required' => 'Debe seleccionar un vendedor.',
            'items.min' => 'La factura debe tener al menos una línea.',
            'items.*.descripcion.required' => 'La descripción es obligatoria.',
            'items.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'items.*.descuento_porcentaje.max' => 'El descuento de la línea supera el máximo permitido para el vendedor.',
        ];
    }

    public function guardar()
    {
        $puedeDescontar = Gate::allows('facturas.descuento');

        // Si no tiene permiso para descuentos, se fuerzan a 0
        if (! $puedeDescontar) {
            foreach ($this->items as &$item) {
                $item['descuento_porcentaje'] = 0;
            }
            unset($item);
            $this->descuento_porcentaje = 0;
        }

        $this->validate();

        $facturaService = app(FacturaService::class);

        return $facturaService->crear($this->all());
    }
}
