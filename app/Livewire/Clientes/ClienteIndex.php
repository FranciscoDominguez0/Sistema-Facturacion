<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ClienteIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filtroEstado = 'Todos';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    /**
     * Desactivar/activar es la única vía de "eliminación":
     * los clientes con facturas nunca se borran físicamente.
     */
    public function toggleActivo(Cliente $cliente)
    {
        $this->authorize('clientes.editar');

        $cliente->update(['activo' => ! $cliente->activo]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $clientes = Cliente::query()
            ->when($this->search, function ($query) {
                $query->where(fn ($q) => $q
                    ->where('nombre', 'like', '%'.$this->escapeLike($this->search).'%')
                    ->orWhere('identificacion', 'like', '%'.$this->escapeLike($this->search).'%')
                    ->orWhere('email', 'like', '%'.$this->escapeLike($this->search).'%'));
            })
            ->when($this->filtroEstado !== 'Todos', function ($query) {
                $query->where('activo', $this->filtroEstado === 'Activo');
            })
            ->latest()
            ->paginate(config('paginacion.por_pagina'));

        return view('livewire.clientes.cliente-index', [
            'clientes' => $clientes,
        ]);
    }

    /**
     * Escapa los comodines del término de búsqueda para que el LIKE
     * no los interprete como patrones.
     */
    protected function escapeLike(string $termino): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $termino);
    }
}
