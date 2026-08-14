<?php

namespace App\Livewire\Productos;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProductoForm extends Component
{
    use \Livewire\WithFileUploads;

    public \App\Livewire\Forms\ProductoForm $form;
    public ?\App\Models\Producto $producto = null;
    public $imagen;
    public $isEdit = false;

    public function mount(?\App\Models\Producto $producto = null)
    {
        if ($producto && $producto->exists) {
            $this->producto = $producto;
            $this->isEdit = true;
            $this->form->setProducto($producto);
        }
    }

    public function save()
    {
        if ($this->imagen) {
            $this->validate([
                'imagen' => 'image|max:2048', // 2MB Max
            ]);
            
            // Eliminar imagen anterior si existe
            if ($this->isEdit && $this->producto->imagen_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->producto->imagen_path);
            }
            
            $path = $this->imagen->store('productos', 'public');
            $this->form->imagen_path = $path;
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
