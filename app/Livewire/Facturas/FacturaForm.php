<?php

namespace App\Livewire\Facturas;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Vendedor;
use App\Services\FacturaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class FacturaForm extends Component
{
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

    // Para la creación rápida de cliente
    public $nuevo_cliente_nombre = '';
    public $nuevo_cliente_identificacion = '';
    public $nuevo_cliente_email = '';
    public $nuevo_cliente_telefono = '';
    public $nuevo_cliente_direccion = '';
    public $mostrarModalCliente = false;

    // Para la creación rápida de producto
    public $nuevo_producto_nombre = '';
    public $nuevo_producto_precio = '';
    public $nuevo_producto_tipo = 'bien';
    public $nuevo_producto_aplica_impuesto = true;
    public $mostrarModalProducto = false;
    public $linea_producto_actual = null;

    // Para búsquedas
    public $searchCliente = '';
    public $clientes_sugeridos = [];
    public $cliente_seleccionado_nombre = '';

    protected FacturaService $facturaService;

    public function boot(FacturaService $facturaService)
    {
        $this->facturaService = $facturaService;
    }

    public function mount()
    {
        $this->fecha_emision = date('Y-m-d');
        
        // Autoseleccionar vendedor si no puede elegirlo libremente
        if (!Gate::allows('facturas.vendedor.seleccionar')) {
            $vendedor = Auth::user()->vendedor;
            if ($vendedor) {
                $this->vendedor_id = $vendedor->id;
            }
        }

        // Línea inicial vacía
        $this->agregarLinea();
    }

    public function updatedSearchCliente($value)
    {
        if (strlen($value) >= 2) {
            $this->clientes_sugeridos = Cliente::where('nombre', 'ilike', '%' . $value . '%')
                ->take(5)
                ->get()
                ->toArray();
        } else {
            $this->clientes_sugeridos = [];
        }
    }

    public function seleccionarCliente($id, $nombre)
    {
        $this->cliente_id = $id;
        $this->cliente_seleccionado_nombre = $nombre;
        $this->searchCliente = '';
        $this->clientes_sugeridos = [];
    }

    public function deseleccionarCliente()
    {
        $this->cliente_id = null;
        $this->cliente_seleccionado_nombre = '';
    }

    public function guardarClienteExpress()
    {
        $this->validate([
            'nuevo_cliente_nombre' => 'required|string|max:255',
            'nuevo_cliente_identificacion' => 'nullable|string|max:255',
            'nuevo_cliente_email' => 'nullable|email|max:255',
            'nuevo_cliente_telefono' => 'nullable|string|max:255',
            'nuevo_cliente_direccion' => 'nullable|string|max:1000',
        ]);

        $cliente = Cliente::create([
            'nombre' => $this->nuevo_cliente_nombre,
            'identificacion' => $this->nuevo_cliente_identificacion,
            'email' => $this->nuevo_cliente_email,
            'telefono' => $this->nuevo_cliente_telefono,
            'direccion' => $this->nuevo_cliente_direccion,
            'activo' => true,
        ]);

        $this->seleccionarCliente($cliente->id, $cliente->nombre);
        $this->mostrarModalCliente = false;
        
        $this->nuevo_cliente_nombre = '';
        $this->nuevo_cliente_identificacion = '';
        $this->nuevo_cliente_email = '';
        $this->nuevo_cliente_telefono = '';
        $this->nuevo_cliente_direccion = '';
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

    public function updatedItems()
    {
        $this->recalcularTotales();
    }

    public function updatedDescuentoPorcentaje()
    {
        $this->recalcularTotales();
    }

    public function seleccionarProducto($index, $productoId)
    {
        if ($productoId === 'nuevo_producto') {
            $this->items[$index]['producto_id'] = null; // reset select
            $this->linea_producto_actual = $index;
            $this->mostrarModalProducto = true;
            return;
        }

        if ($productoId) {
            $producto = Producto::find($productoId);
            if ($producto) {
                $this->items[$index]['producto_id'] = $producto->id;
                $this->items[$index]['descripcion'] = $producto->nombre;
                $this->items[$index]['precio_unitario'] = $producto->precio;
                $this->items[$index]['aplica_impuesto'] = $producto->aplica_impuesto;
            }
        } else {
            $this->items[$index]['producto_id'] = null;
        }
        $this->recalcularTotales();
    }

    public function guardarProductoExpress()
    {
        $this->validate([
            'nuevo_producto_nombre' => 'required|string|max:255',
            'nuevo_producto_precio' => 'required|numeric|min:0',
            'nuevo_producto_tipo' => 'required|in:bien,servicio',
        ]);

        $producto = Producto::create([
            'nombre' => $this->nuevo_producto_nombre,
            'precio' => $this->nuevo_producto_precio,
            'aplica_impuesto' => $this->nuevo_producto_aplica_impuesto,
            'tipo' => $this->nuevo_producto_tipo,
            'activo' => true,
        ]);

        if ($this->linea_producto_actual !== null) {
            $this->items[$this->linea_producto_actual]['producto_id'] = $producto->id;
            $this->items[$this->linea_producto_actual]['descripcion'] = $producto->nombre;
            $this->items[$this->linea_producto_actual]['precio_unitario'] = $producto->precio;
            $this->items[$this->linea_producto_actual]['aplica_impuesto'] = $producto->aplica_impuesto;
            $this->recalcularTotales();
        }

        $this->mostrarModalProducto = false;
        
        $this->nuevo_producto_nombre = '';
        $this->nuevo_producto_precio = '';
        $this->nuevo_producto_tipo = 'bien';
        $this->nuevo_producto_aplica_impuesto = true;
        $this->linea_producto_actual = null;
    }

    protected function recalcularTotales()
    {
        $empresa = Empresa::first();
        $impuestoPorcentaje = $empresa ? floatval($empresa->impuesto_porcentaje) : 0;

        $resultado = $this->facturaService->calcularTotales(
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

    public function save()
    {
        $this->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vendedor_id' => 'required|exists:vendedores,id',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.descripcion' => 'required|string',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ], [
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'vendedor_id.required' => 'Debe seleccionar un vendedor.',
            'items.min' => 'La factura debe tener al menos una línea.',
            'items.*.descripcion.required' => 'La descripción es obligatoria.',
            'items.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
        ]);

        $factura = $this->facturaService->crear([
            'cliente_id' => $this->cliente_id,
            'vendedor_id' => $this->vendedor_id,
            'fecha_emision' => $this->fecha_emision,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'descuento_porcentaje' => $this->descuento_porcentaje,
            'notas' => $this->notas,
            'items' => $this->items,
        ]);

        return redirect()->route('facturas.show', $factura->id);
    }

    public function render()
    {
        return view('livewire.facturas.factura-form', [
            'vendedores' => Gate::allows('facturas.vendedor.seleccionar') ? Vendedor::with('user')->get() : collect(),
            'productos' => Producto::where('activo', true)->get(),
        ]);
    }
}
