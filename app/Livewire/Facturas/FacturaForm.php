<?php

namespace App\Livewire\Facturas;

use App\Livewire\Forms\FacturaForm as FacturaFormObject;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Impuesto;
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

    public $nuevo_producto_impuesto_id = null;

    public $mostrarModalProducto = false;

    public $linea_producto_actual = null;

    // Para la creación rápida de impuesto
    public $mostrarModalImpuesto = false;

    public $linea_impuesto_actual = null;

    public $nuevo_impuesto_nombre = '';

    public $nuevo_impuesto_porcentaje = '';

    // Para la creación rápida de vendedor
    public $mostrarModalVendedor = false;

    public $nuevo_vendedor_nombre = '';

    public $nuevo_vendedor_email = '';

    public $nuevo_vendedor_password = '';

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
        }

        // Si viene un cliente_id por la URL
        if ($this->cliente_id) {
            $cliente = Cliente::find($this->cliente_id);
            if ($cliente) {
                $this->form->cliente_id = $cliente->id;
            } else {
                $this->cliente_id = null;
            }
        }
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

        $this->form->cliente_id = $cliente->id;
        $this->cliente_id = $cliente->id;
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
        // Sincronizar el cliente seleccionado en el form object
        if ($property === 'cliente_id') {
            $this->form->cliente_id = $value;
            return;
        }

        if (str_starts_with($property, 'form.items') || $property === 'form.descuento_porcentaje') {
            if (str_ends_with($property, '.producto_id')) {
                preg_match('/form\.items\.(\d+)\.producto_id/', $property, $matches);
                if (isset($matches[1])) {
                    $index = $matches[1];
                    if ($value === 'nuevo_producto') {
                        $this->form->items[$index]['producto_id'] = null;
                        $this->linea_producto_actual = $index;
                        $this->mostrarModalProducto = true;
                    } elseif ($value) {
                        $producto = Producto::with('impuesto')->find($value);
                        if ($producto) {
                            $this->form->items[$index]['precio_unitario'] = $producto->precio;
                            // Descuento configurado en el producto (0 si no lleva).
                            $this->form->items[$index]['descuento_porcentaje'] = $producto->descuento_porcentaje ?? 0;
                            $this->form->items[$index]['impuesto_id'] = $producto->impuesto_id;
                            $this->form->items[$index]['impuesto_nombre'] = $producto->impuesto ? $producto->impuesto->nombre : null;
                            $this->form->items[$index]['impuesto_porcentaje'] = $producto->impuesto ? $producto->impuesto->porcentaje : 0;
                        }
                    } else {
                        $this->form->items[$index]['precio_unitario'] = 0;
                    }
                }
            } elseif (str_ends_with($property, '.impuesto_id')) {
                preg_match('/form\.items\.(\d+)\.impuesto_id/', $property, $matches);
                if (isset($matches[1])) {
                    $index = $matches[1];
                    // Cada línea tiene su propio impuesto: al cambiarlo se actualizan
                    // el nombre y porcentaje (null = producto exento).
                    $impuesto = $value ? Impuesto::find($value) : null;
                    $this->form->items[$index]['impuesto_id'] = $impuesto?->id;
                    $this->form->items[$index]['impuesto_nombre'] = $impuesto?->nombre;
                    $this->form->items[$index]['impuesto_porcentaje'] = $impuesto?->porcentaje ?? 0;
                }
            } elseif (str_ends_with($property, '.descuento_porcentaje')) {
                preg_match('/form\.items\.(\d+)\.descuento_porcentaje/', $property, $matches);
                if (isset($matches[1])) {
                    // El selector de descuento entrega el porcentaje como texto.
                    $this->form->items[$matches[1]]['descuento_porcentaje'] = floatval($value ?: 0);
                }
            }
            $this->form->recalcularTotales();
        }

        if ($property === 'form.vendedor_id' && $value === 'nuevo_vendedor') {
            $this->form->vendedor_id = null;
            $this->mostrarModalVendedor = true;
        }
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
            'impuesto_id' => $this->nuevo_producto_impuesto_id,
            'tipo' => $this->nuevo_producto_tipo,
            'activo' => true,
        ]);

        if ($this->linea_producto_actual !== null) {
            $producto->load('impuesto');
            $this->form->items[$this->linea_producto_actual]['producto_id'] = $producto->id;
            $this->form->items[$this->linea_producto_actual]['precio_unitario'] = $producto->precio;
            $this->form->items[$this->linea_producto_actual]['impuesto_id'] = $producto->impuesto_id;
            $this->form->items[$this->linea_producto_actual]['impuesto_nombre'] = $producto->impuesto ? $producto->impuesto->nombre : null;
            $this->form->items[$this->linea_producto_actual]['impuesto_porcentaje'] = $producto->impuesto ? $producto->impuesto->porcentaje : 0;
            $this->form->recalcularTotales();
        }

        $this->mostrarModalProducto = false;

        $this->nuevo_producto_nombre = '';
        $this->nuevo_producto_precio = '';
        $this->nuevo_producto_tipo = 'bien';
        $this->nuevo_producto_impuesto_id = null;
        $this->linea_producto_actual = null;
    }

    public function guardarImpuestoExpress()
    {
        $this->validate([
            'nuevo_impuesto_nombre' => 'required|string|max:255',
            'nuevo_impuesto_porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        $impuesto = \App\Models\Impuesto::create([
            'nombre' => $this->nuevo_impuesto_nombre,
            'porcentaje' => $this->nuevo_impuesto_porcentaje,
            'activo' => true,
        ]);

        if ($this->linea_impuesto_actual !== null) {
            $this->form->items[$this->linea_impuesto_actual]['impuesto_id'] = $impuesto->id;
            $this->form->items[$this->linea_impuesto_actual]['impuesto_nombre'] = $impuesto->nombre;
            $this->form->items[$this->linea_impuesto_actual]['impuesto_porcentaje'] = $impuesto->porcentaje;
            $this->form->recalcularTotales();
        }

        $this->mostrarModalImpuesto = false;
        $this->nuevo_impuesto_nombre = '';
        $this->nuevo_impuesto_porcentaje = '';
        $this->linea_impuesto_actual = null;
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

        $this->form->vendedor_id = $vendedor->id;
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

            // navigate: solo se actualiza el contenido, el sidebar no se recarga
            return $this->redirectRoute('facturas.show', $factura->id, navigate: true);
        } catch (ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Validation errors al crear factura', $e->errors());
            $this->dispatch('toast', message: 'Hay campos obligatorios vacíos o con errores. Por favor, revisa el formulario.', type: 'error');
            throw $e;
        }
    }

    public function render()
    {
        $impuestos = Impuesto::where('activo', true)->orderBy('porcentaje')->get();

        return view('livewire.facturas.factura-form', [
            'clientes' => Cliente::where('activo', true)->get(['id', 'nombre'])->toArray(),
            'vendedores' => Gate::allows('facturas.vendedor.seleccionar') ? User::role('Vendedor')->get(['id', 'name as nombre'])->toArray() : [],
            'productos' => Producto::where('activo', true)->get(['id', 'nombre', 'precio', 'impuesto_id'])->toArray(),
            // Opciones del selector de impuesto por línea: "Exento" + los activos.
            'opcionesImpuestos' => collect([['id' => '', 'nombre' => 'Exento']])
                ->concat($impuestos->map(fn (Impuesto $impuesto) => [
                    'id' => (string) $impuesto->id,
                    'nombre' => $impuesto->nombre.' ('.number_format($impuesto->porcentaje, 2).'%)',
                ]))
                ->all(),
            // Opciones del selector de descuento por línea.
            'opcionesDescuentos' => collect([0, 5, 10, 15, 20, 25, 30, 40, 50])
                ->map(fn ($porcentaje) => [
                    'id' => (string) $porcentaje,
                    'nombre' => $porcentaje.'%',
                ])
                ->all(),
        ]);
    }
}
