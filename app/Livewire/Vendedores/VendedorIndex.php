<?php

namespace App\Livewire\Vendedores;

use App\Models\Vendedor;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class VendedorIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filtroEstado = 'Todos';

    public function updated($property)
    {
        if (in_array($property, ['search', 'filtroEstado'])) {
            $this->resetPage();
        }
    }

    public function toggleActivo(int $id)
    {
        $this->authorize('vendedores.gestionar');

        $vendedor = Vendedor::findOrFail($id);
        $vendedor->update(['activo' => ! $vendedor->activo]);
    }

    public function render()
    {
        $vendedores = Vendedor::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($u) {
                        $u->where('name', 'ilike', '%'.$this->escapeLike($this->search).'%')
                            ->orWhere('email', 'ilike', '%'.$this->escapeLike($this->search).'%');
                    })->orWhere('codigo', 'ilike', '%'.$this->escapeLike($this->search).'%');
                });
            })
            ->when($this->filtroEstado !== 'Todos', function ($query) {
                $query->where('activo', $this->filtroEstado === 'Activo');
            })
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('livewire.vendedores.vendedor-index', [
            'vendedores' => $vendedores,
        ]);
    }

    protected function escapeLike(string $termino): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $termino);
    }
}
