<?php

namespace App\Livewire\Productos;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProductoIndex extends Component
{
    public function render()
    {
        return view('livewire.productos.producto-index');
    }
}
