<?php

namespace App\Livewire\Facturas;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FacturaIndex extends Component
{
    public function render()
    {
        return view('livewire.facturas.factura-index');
    }
}
