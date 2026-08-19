<?php

namespace App\Livewire\Facturas;

use App\Enums\EstadoFactura;
use App\Models\Factura;
use App\Services\FacturaService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FacturaShow extends Component
{
    public Factura $factura;

    public function mount(Factura $factura)
    {
        $this->factura = $factura->load(['cliente', 'vendedor.user', 'items.producto']);
    }

    public function cambiarEstado(string $nuevoEstado, FacturaService $facturaService)
    {
        if (! Gate::allows('facturas.estado.cambiar')) {
            abort(403, 'No tiene permiso para cambiar el estado de la factura.');
        }

        try {
            $estadoEnum = EstadoFactura::from($nuevoEstado);
            $this->factura = $facturaService->cambiarEstado($this->factura, $estadoEnum);
            session()->flash('success', 'Estado de la factura actualizado correctamente.');
        } catch (\DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.facturas.factura-show');
    }
}
