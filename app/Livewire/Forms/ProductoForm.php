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

    public string $tipo = 'producto';

    public bool $aplica_impuesto = true;

    public string $imagen_path = '';

    public bool $activo = true;

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'codigo' => ['nullable', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'tipo' => ['required', 'string', 'in:producto,servicio'],
            'aplica_impuesto' => ['boolean'],
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
        $this->tipo = $producto->tipo;
        $this->aplica_impuesto = $producto->aplica_impuesto;
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
        $this->validate();

        Producto::create($this->only([
            'nombre',
            'descripcion',
            'codigo',
            'precio',
            'tipo',
            'aplica_impuesto',
            'imagen_path',
            'activo',
        ]));

        $this->reset();
    }

    public function update()
    {
        $this->normalizar();
        $this->validate();

        $this->producto->update($this->only([
            'nombre',
            'descripcion',
            'codigo',
            'precio',
            'tipo',
            'aplica_impuesto',
            'imagen_path',
            'activo',
        ]));
    }
}
