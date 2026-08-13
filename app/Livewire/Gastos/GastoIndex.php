<?php

namespace App\Livewire\Gastos;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GastoIndex extends Component
{
    public function render()
    {
        return view('livewire.gastos.gasto-index');
    }
}
