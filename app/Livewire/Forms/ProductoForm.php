<?php

namespace App\Livewire\Forms;

use App\Models\Producto;
use Illuminate\Support\Str;
use Livewire\Form;

class ProductoForm extends Form
{
    public ?Producto $producto = null;

    public string $nombre = '';

    public string $descripcion = '';

    public string $codigo = '';

    public string $precio = '';

    public string $descuento_porcentaje = '0';

    public string $tipo = 'producto';

    public ?int $impuesto_id = null;

    public string $imagen_path = '';

    public bool $activo = true;

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'codigo' => ['nullable', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'descuento_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tipo' => ['required', 'string', 'in:producto,servicio'],
            'impuesto_id' => ['nullable', 'exists:impuestos,id'],
            'imagen_path' => ['nullable', 'string'],
            'activo' => ['boolean'],
        ];
    }

    public function setProducto(Producto $producto)
    {
        $this->producto = $producto;
        $this->nombre = $producto->nombre;
        $this->descripcion = $producto->descripcion ?? '';
        $this->codigo = $producto->codigo ?? '';
        $this->precio = $producto->precio;
        $this->descuento_porcentaje = (string) ($producto->descuento_porcentaje ?? 0);
        $this->tipo = $producto->tipo;
        $this->impuesto_id = $producto->impuesto_id;
        $this->imagen_path = $producto->imagen_path ?? '';
        $this->activo = $producto->activo;
    }

    public function normalizar()
    {
        $this->nombre = Str::squish(trim($this->nombre));
        $this->descripcion = trim($this->descripcion);
        $this->codigo = trim($this->codigo);
    }

    public function store()
    {
        $this->normalizar();
        $validated = $this->validate();

        Producto::create($validated);

        $this->reset();
    }

    public function update()
    {
        $this->normalizar();
        $validated = $this->validate();

        $this->producto->update($validated);
    }
}
