<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProductoIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filtroEstado = 'Todos';

    public string $filtroTipo = 'Todos';

    public function updated($property)
    {
        if (in_array($property, ['search', 'filtroEstado', 'filtroTipo'])) {
            $this->resetPage();
        }
    }

    public function toggleActivo(int $id)
    {
        $this->authorize('productos.editar');

        $producto = Producto::findOrFail($id);
        $producto->update(['activo' => ! $producto->activo]);
    }

    public function render()
    {
        $productos = Producto::query()
            ->with('impuesto')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'ilike', '%'.$this->escapeLike($this->search).'%')
                        ->orWhere('codigo', 'ilike', '%'.$this->escapeLike($this->search).'%');
                });
            })
            ->when($this->filtroEstado !== 'Todos', function ($query) {
                $query->where('activo', $this->filtroEstado === 'Activo');
            })
            ->when($this->filtroTipo !== 'Todos', function ($query) {
                $query->where('tipo', strtolower($this->filtroTipo));
            })
            ->orderBy('id', 'desc')
            ->paginate(config('paginacion.por_pagina'));

        return view('livewire.productos.producto-index', [
            'productos' => $productos,
        ]);
    }

    protected function escapeLike(string $termino): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $termino);
    }
}
