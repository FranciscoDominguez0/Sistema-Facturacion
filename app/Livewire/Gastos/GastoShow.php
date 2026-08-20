<?php

namespace App\Livewire\Gastos;

use App\Models\Gasto;
use Livewire\Attributes\Layout;
use Livewire\Component;

class GastoShow extends Component
{
    public Gasto $gasto;

    public function mount(Gasto $gasto)
    {
        $this->gasto = $gasto;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.gastos.gasto-show');
    }
}
