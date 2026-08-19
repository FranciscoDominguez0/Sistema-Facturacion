<?php

namespace App\Livewire\Facturas;

use App\Livewire\Forms\FacturaForm as FacturaFormObject;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Vendedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class FacturaForm extends Component
{
    public FacturaFormObject $form;

    #[Url]
    public $cliente_id;

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

    public function mount()
    {
        $this->form->init();

        // Autoseleccionar vendedor si no puede elegirlo libremente
        if (! Gate::allows('facturas.vendedor.seleccionar')) {
            $vendedor = Auth::user()->vendedor;
            if ($vendedor) {
                $this->form->vendedor_id = $vendedor->id;
            }
        }

        // Si viene un cliente_id por la URL
        if ($this->cliente_id) {
            $cliente = Cliente::find($this->cliente_id);
            if ($cliente) {
                $this->form->cliente_id = $cliente->id;
                $this->cliente_seleccionado_nombre = $cliente->nombre;
            } else {
                $this->cliente_id = null;
            }
        }
    }

    public function updatedSearchCliente($value)
    {
        if (strlen($value) >= 2) {
            $this->clientes_sugeridos = Cliente::where('nombre', 'ilike', '%'.$value.'%')
                ->take(5)
                ->get()
                ->toArray();
        } else {
            $this->clientes_sugeridos = [];
        }
    }

    public function seleccionarCliente($id, $nombre)
    {
        $this->form->cliente_id = $id;
        $this->cliente_id = $id;
        $this->cliente_seleccionado_nombre = $nombre;
        $this->searchCliente = '';
        $this->clientes_sugeridos = [];
    }

    public function deseleccionarCliente()
    {
        $this->form->cliente_id = null;
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
        $this->form->agregarLinea();
    }

    public function eliminarLinea($index)
    {
        $this->form->eliminarLinea($index);
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'form.items') || $property === 'form.descuento_porcentaje') {
            $this->form->recalcularTotales();
        }
    }

    public function seleccionarProducto($index, $productoId)
    {
        if ($productoId === 'nuevo_producto') {
            $this->form->items[$index]['producto_id'] = null;
            $this->linea_producto_actual = $index;
            $this->mostrarModalProducto = true;

            return;
        }

        if ($productoId) {
            $producto = Producto::find($productoId);
            if ($producto) {
                $this->form->items[$index]['producto_id'] = $producto->id;
                $this->form->items[$index]['descripcion'] = $producto->nombre;
                $this->form->items[$index]['precio_unitario'] = $producto->precio;
                $this->form->items[$index]['aplica_impuesto'] = $producto->aplica_impuesto;
            }
        } else {
            $this->form->items[$index]['producto_id'] = null;
        }
        $this->form->recalcularTotales();
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
            $this->form->items[$this->linea_producto_actual]['producto_id'] = $producto->id;
            $this->form->items[$this->linea_producto_actual]['descripcion'] = $producto->nombre;
            $this->form->items[$this->linea_producto_actual]['precio_unitario'] = $producto->precio;
            $this->form->items[$this->linea_producto_actual]['aplica_impuesto'] = $producto->aplica_impuesto;
            $this->form->recalcularTotales();
        }

        $this->mostrarModalProducto = false;

        $this->nuevo_producto_nombre = '';
        $this->nuevo_producto_precio = '';
        $this->nuevo_producto_tipo = 'bien';
        $this->nuevo_producto_aplica_impuesto = true;
        $this->linea_producto_actual = null;
    }

    public function save()
    {
        $factura = $this->form->guardar();
        session()->flash('success', 'Factura creada exitosamente.');

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
