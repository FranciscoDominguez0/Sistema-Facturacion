<?php

namespace App\Livewire\Facturas;

use App\Livewire\Forms\FacturaForm as FacturaFormObject;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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

    // Para la creación rápida de vendedor
    public $mostrarModalVendedor = false;

    public $nuevo_vendedor_nombre = '';

    public $nuevo_vendedor_email = '';

    public $nuevo_vendedor_password = '';

    // Para búsquedas cliente
    public $searchCliente = '';

    public $clientes_sugeridos = [];

    public $cliente_seleccionado_nombre = '';

    // Para búsquedas vendedor
    public $searchVendedor = '';

    public $vendedores_sugeridos = [];

    public $vendedor_seleccionado_nombre = '';

    public $numero_factura_preview = '';

    public function mount()
    {
        $this->form->init();

        $empresa = Empresa::first();
        if ($empresa) {
            $numero = $empresa->siguiente_numero_factura;
            $prefijo = $empresa->prefijo_factura;
            $this->numero_factura_preview = $prefijo.str_pad((string) $numero, 6, '0', STR_PAD_LEFT);
        } else {
            $this->numero_factura_preview = 'FAC-000001';
        }

        // Autoseleccionar vendedor si no puede elegirlo libremente
        if (! Gate::allows('facturas.vendedor.seleccionar')) {
            $this->form->vendedor_id = Auth::id();
            $this->vendedor_seleccionado_nombre = Auth::user()->name;
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

    public function updatedSearchVendedor($value)
    {
        if (strlen($value) >= 2) {
            $this->vendedores_sugeridos = User::role('Vendedor')
                ->where('name', 'ilike', '%'.$value.'%')
                ->take(5)
                ->get()
                ->toArray();
        } else {
            $this->vendedores_sugeridos = [];
        }
    }

    public function seleccionarVendedor($id, $nombre)
    {
        $this->form->vendedor_id = $id;
        $this->vendedor_seleccionado_nombre = $nombre;
        $this->searchVendedor = '';
        $this->vendedores_sugeridos = [];
    }

    public function deseleccionarVendedor()
    {
        $this->form->vendedor_id = null;
        $this->vendedor_seleccionado_nombre = '';
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

    public function updated($property, $value)
    {
        if (str_starts_with($property, 'form.items') || $property === 'form.descuento_porcentaje') {
            $this->form->recalcularTotales();
        }

        if ($property === 'form.vendedor_id' && $value === 'nuevo_vendedor') {
            $this->form->vendedor_id = null;
            $this->mostrarModalVendedor = true;
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

    public function guardarVendedorExpress()
    {
        $this->validate([
            'nuevo_vendedor_nombre' => 'required|string|max:255',
            'nuevo_vendedor_email' => 'nullable|email|unique:users,email|max:255',
            'nuevo_vendedor_password' => 'nullable|string|min:8',
        ]);

        $vendedor = User::create([
            'name' => $this->nuevo_vendedor_nombre,
            'email' => empty($this->nuevo_vendedor_email) ? null : $this->nuevo_vendedor_email,
            'password' => empty($this->nuevo_vendedor_password) ? null : Hash::make($this->nuevo_vendedor_password),
        ]);

        $vendedor->assignRole('Vendedor');

        $this->seleccionarVendedor($vendedor->id, $vendedor->name);
        $this->mostrarModalVendedor = false;

        $this->nuevo_vendedor_nombre = '';
        $this->nuevo_vendedor_email = '';
        $this->nuevo_vendedor_password = '';
    }

    public function save()
    {
        try {
            $factura = $this->form->guardar();
            session()->flash('success', 'Factura creada exitosamente.');

            return redirect()->route('facturas.show', $factura->id);
        } catch (ValidationException $e) {
            $this->dispatch('toast', message: 'Hay campos obligatorios vacíos o con errores. Por favor, revisa el formulario.', type: 'error');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.facturas.factura-form', [
            'vendedores' => Gate::allows('facturas.vendedor.seleccionar') ? User::role('Vendedor')->get() : collect(),
            'productos' => Producto::where('activo', true)->get(),
        ]);
    }
}
