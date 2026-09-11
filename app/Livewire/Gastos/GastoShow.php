<?php

namespace App\Livewire\Gastos;

use App\Models\Gasto;
use Livewire\Attributes\Layout;
use Livewire\Component;

class GastoShow extends Component
{
    public Gasto $gasto;

    public bool $confirmingDeletion = false;

    public function mount(Gasto $gasto)
    {
        $this->gasto = $gasto;
    }

    public function confirmarEliminacion()
    {
        $this->confirmingDeletion = true;
    }

    public function eliminar()
    {
        $this->gasto->delete();
        session()->flash('success', 'Gasto eliminado exitosamente.');

        return $this->redirectRoute('gastos', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.gastos.gasto-show');
    }
}
