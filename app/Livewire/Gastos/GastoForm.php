<?php

namespace App\Livewire\Gastos;

use App\Livewire\Forms\GastoForm as GastoFormObject;
use App\Models\Gasto;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GastoForm extends Component
{
    public GastoFormObject $form;

    // Gasto que se está editando (null = registro nuevo)
    public ?Gasto $gasto = null;

    public function mount(?Gasto $gasto = null): void
    {
        $this->gasto = $gasto;

        if ($gasto) {
            $this->form->cargarGasto($gasto);
        } else {
            $this->form->fecha = now()->format('Y-m-d');
        }
    }

    public function guardar()
    {
        // En edición se exige el permiso de editar; en creación, el de crear
        $permiso = $this->gasto ? 'gastos.editar' : 'gastos.crear';
        $this->authorize($permiso);

        if ($this->gasto) {
            $this->form->actualizar($this->gasto);
            session()->flash('success', 'Gasto actualizado con éxito.');
        } else {
            $this->form->guardar();
            session()->flash('success', 'Gasto registrado con éxito.');
        }

        return $this->redirect(route('gastos'), navigate: true);
    }

    public function render()
    {
        return view('livewire.gastos.gasto-form');
    }
}
