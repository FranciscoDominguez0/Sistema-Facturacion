<?php

namespace App\Livewire\Gastos;

use App\Models\Gasto;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class GastoIndex extends Component
{
    use WithPagination;

    public string $search = '';

    // Gasto elegido para eliminar (se muestra en el modal de confirmación)
    public ?Gasto $gastoAEliminar = null;

    public bool $modalEliminarVisible = false;

    public function updated($property)
    {
        if ($property === 'search') {
            $this->resetPage();
        }
    }

    public function confirmarEliminacion($gastoId)
    {
        $this->gastoAEliminar = Gasto::findOrFail($gastoId);
        $this->modalEliminarVisible = true;
    }

    public function eliminar()
    {
        if (! Gate::allows('gastos.eliminar')) {
            abort(403, 'No tiene permiso para eliminar gastos.');
        }

        if ($this->gastoAEliminar) {
            $this->gastoAEliminar->delete();
        }

        $this->modalEliminarVisible = false;
        $this->gastoAEliminar = null;

        $this->dispatch('toast', message: 'Gasto eliminado correctamente.', type: 'success');
    }

    public function render()
    {
        $gastos = Gasto::with('registradoPor')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('concepto', 'ilike', '%'.$this->escapeLike($this->search).'%')
                        ->orWhere('categoria', 'ilike', '%'.$this->escapeLike($this->search).'%');
                });
            })
            ->recientes()
            ->paginate(config('paginacion.por_pagina'));

        return view('livewire.gastos.gasto-index', [
            'gastos' => $gastos,
        ]);
    }

    protected function escapeLike(string $termino): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $termino);
    }
}
