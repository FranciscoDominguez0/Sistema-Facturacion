<?php

namespace App\Livewire\Configuracion;

use Livewire\Component;
use Livewire\Attributes\Layout;

class SeguridadIndex extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.configuracion.seguridad-index');
    }
}
