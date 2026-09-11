<?php

namespace App\Livewire\Facturas;

use App\Models\Factura;
use App\Services\FacturaService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FacturaIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $filtroEstado = 'Todos';

    // Factura elegida para eliminar (se muestra en el modal de confirmación)
    public ?Factura $facturaAEliminar = null;

    public bool $modalEliminarVisible = false;

    // Factura cuyo PDF se carga en el iframe oculto de impresión
    public ?Factura $facturaPdfVista = null;

    // Cambia en cada impresión para forzar la recarga del PDF en el iframe
    public int $impresionToken = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function enviarPorCorreo($facturaId)
    {
        $factura = Factura::with('cliente')->findOrFail($facturaId);

        if (! app(FacturaService::class)->enviarPorCorreo($factura)) {
            $this->dispatch('toast', message: 'El cliente no tiene correo electrónico registrado.', type: 'error');

            return;
        }

        $this->dispatch('toast', message: 'Factura '.$factura->numero_factura.' enviada a '.$factura->cliente->email.'.', type: 'success');
    }

    public function confirmarEliminacion($facturaId)
    {
        $this->facturaAEliminar = Factura::findOrFail($facturaId);
        $this->modalEliminarVisible = true;
    }

    /**
     * Prepara el PDF oculto y abre el diálogo de impresión al cargar
     * (el iframe oculto de la vista imprime en su evento @load).
     */
    public function abrirImpresion($facturaId)
    {
        $this->facturaPdfVista = Factura::with('cliente')->findOrFail($facturaId);
        $this->impresionToken++;
    }

    public function eliminar(FacturaService $facturaService)
    {
        if (! Gate::allows('facturas.eliminar')) {
            abort(403, 'No tiene permiso para eliminar facturas.');
        }

        if ($this->facturaAEliminar) {
            $facturaService->eliminar($this->facturaAEliminar);
        }

        $this->modalEliminarVisible = false;
        $this->facturaAEliminar = null;

        $this->dispatch('toast', message: 'Factura eliminada correctamente.', type: 'success');
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
