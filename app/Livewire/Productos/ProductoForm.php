<?php

namespace App\Livewire\Productos;

use App\Livewire\Forms\ProductoForm as ProductoFormObject;
use App\Models\Producto;
use App\Services\ProductoImagenService;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ProductoForm extends Component
{
    use WithFileUploads;

    public ProductoFormObject $form;

    public ?Producto $producto = null;

    public UploadedFile|string|null $imagen = null;

    public bool $isEdit = false;

    public function mount(?Producto $producto = null)
    {
        if (! $producto?->exists) {
            return;
        }

        $this->producto = $producto;
        $this->isEdit = true;
        $this->form->setProducto($producto);
    }

    public function save(ProductoImagenService $imagenes)
    {
        $this->authorize('productos.gestionar');

        if ($this->imagen instanceof UploadedFile) {
            $this->form->imagen_path = $imagenes->guardar($this->imagen, $this->producto?->imagen_path);
        }

        if ($this->isEdit) {
            $this->form->update();
            session()->flash('success', 'Producto actualizado correctamente.');
        } else {
            $this->form->store();
            session()->flash('success', 'Producto creado correctamente.');
        }

        return $this->redirect(route('productos.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.productos.producto-form');
    }
}
