<?php

namespace App\Livewire\Facturas;

use App\Models\Factura;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FacturaIndex extends Component
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

    public function render()
    {
        $facturas = Factura::with('cliente')
            ->buscar($this->search)
            ->when($this->filtroEstado !== 'Todos', fn ($query) => $query->where('estado', $this->filtroEstado))
            ->orderBy('id', 'desc')
            ->paginate(config('paginacion.por_pagina'));

        return view('livewire.facturas.factura-index', [
            'facturas' => $facturas,
        ]);
    }
}
