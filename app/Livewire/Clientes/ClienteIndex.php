<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ClienteIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $filtroEstado = 'Todos';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function delete(Cliente $cliente)
    {
        $cliente->delete();
    }

    public function toggleActivo(Cliente $cliente)
    {
        $cliente->update(['activo' => ! $cliente->activo]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $query = Cliente::query()
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('identificacion', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->when($this->filtroEstado !== 'Todos', function ($query) {
                $query->where('activo', $this->filtroEstado === 'Activo' ? true : false);
            })
            ->latest();

        return view('livewire.clientes.cliente-index', [
            'clientes' => $query->paginate(10),
        ]);
    }
}
