<?php

namespace App\Livewire\Gastos;

use App\Livewire\Forms\GastoForm as GastoFormObject;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GastoForm extends Component
{
    public GastoFormObject $form;

    public function mount(): void
    {
        $this->form->fecha = now()->format('Y-m-d');
    }

    public function guardar()
    {
        $this->authorize('gastos.crear');

        $this->form->guardar();

        session()->flash('success', 'Gasto registrado con éxito.');

        return $this->redirect(route('gastos'), navigate: true);
    }

    public function render()
    {
        return view('livewire.gastos.gasto-form');
    }
}
