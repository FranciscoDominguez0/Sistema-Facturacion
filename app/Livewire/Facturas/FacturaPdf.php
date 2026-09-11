<?php

namespace App\Livewire\Facturas;

use App\Models\Factura;
use App\Services\FacturaService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FacturaPdf extends Component
{
    public Factura $factura;

    public function mount(Factura $factura)
    {
        $this->factura = $factura->load(['cliente', 'vendedor']);
    }

    public function enviarPorCorreo()
    {
        if (! app(FacturaService::class)->enviarPorCorreo($this->factura)) {
            $this->dispatch('toast', message: 'El cliente no tiene correo electrónico registrado.', type: 'error');

            return;
        }

        $this->dispatch('toast', message: 'Factura enviada a '.$this->factura->cliente->email.'.', type: 'success');
    }

    public function render()
    {
        return view('livewire.facturas.factura-pdf');
    }
}
