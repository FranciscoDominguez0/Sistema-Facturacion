<?php

namespace App\Livewire\Vendedores;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VendedorIndex extends Component
{
    public function render()
    {
        return view('livewire.vendedores.vendedor-index');
    }
}
