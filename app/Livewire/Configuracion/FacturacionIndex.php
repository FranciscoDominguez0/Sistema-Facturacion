<?php

namespace App\Livewire\Configuracion;

use App\Livewire\Forms\FacturacionForm;
use App\Models\Empresa;
use Livewire\Attributes\Layout;
use Livewire\Component;

class FacturacionIndex extends Component
{
    public FacturacionForm $form;

    public function mount()
    {
        $this->authorize('empresa.gestionar');

        $this->form->setEmpresa(Empresa::actual());
    }

    public function getNumeroFacturaPreviewProperty()
    {
        return $this->form->numeroPreview();
    }

    public function getNumeroPreviewAnteriorProperty()
    {
        return $this->form->numeroPreviewAnterior();
    }

    public function getNumeroPreviewSiguienteProperty()
    {
        return $this->form->numeroPreviewSiguiente();
    }

    public function guardar()
    {
        $this->authorize('empresa.gestionar');

        $this->form->guardar();

        $this->dispatch('toast', message: 'Configuración de facturación actualizada con éxito.', type: 'success');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.configuracion.facturacion-index');
    }
}
