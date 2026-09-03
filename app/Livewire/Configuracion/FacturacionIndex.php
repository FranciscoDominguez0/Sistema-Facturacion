<?php

namespace App\Livewire\Configuracion;

use App\Models\Empresa;
use Livewire\Component;
use Livewire\Attributes\Layout;

class FacturacionIndex extends Component
{
    public $prefijo_factura;
    public $siguiente_numero_factura;

    public function mount()
    {
        $empresa = Empresa::actual();
        $this->prefijo_factura = $empresa->prefijo_factura;
        $this->siguiente_numero_factura = $empresa->siguiente_numero_factura;
    }

    public function guardar()
    {
        $this->validate([
            'prefijo_factura' => 'nullable|string|max:10',
            'siguiente_numero_factura' => 'required|integer|min:1',
        ]);

        $empresa = Empresa::actual();
        $empresa->update([
            'prefijo_factura' => $this->prefijo_factura,
            'siguiente_numero_factura' => $this->siguiente_numero_factura,
        ]);

        $this->dispatch('toast', message: 'Configuración de facturación actualizada con éxito.', type: 'success');
    }

    public function getNumeroFacturaPreviewProperty()
    {
        return $this->prefijo_factura . str_pad((int)$this->siguiente_numero_factura, 6, '0', STR_PAD_LEFT);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.configuracion.facturacion-index');
    }
}
