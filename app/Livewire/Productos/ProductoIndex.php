<?php

namespace App\Livewire\Productos;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProductoIndex extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
    public $filtroEstado = 'Todos';
    public $filtroTipo = 'Todos';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function updatingFiltroTipo()
    {
        $this->resetPage();
    }

    public function toggleActivo(int $id)
    {
        $producto = \App\Models\Producto::findOrFail($id);
        $producto->update(['activo' => !$producto->activo]);
    }

    public function render()
    {
        $productos = \App\Models\Producto::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'ilike', '%' . $this->search . '%')
                      ->orWhere('codigo', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->filtroEstado !== 'Todos', function ($query) {
                $query->where('activo', $this->filtroEstado === 'Activo');
            })
            ->when($this->filtroTipo !== 'Todos', function ($query) {
                $query->where('tipo', strtolower($this->filtroTipo));
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.productos.producto-index', [
            'productos' => $productos,
        ]);
    }
}
