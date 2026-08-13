<?php

namespace App\Livewire\Clientes;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ClienteIndex extends Component
{
    public function render()
    {
        return view('livewire.clientes.cliente-index');
    }
}
