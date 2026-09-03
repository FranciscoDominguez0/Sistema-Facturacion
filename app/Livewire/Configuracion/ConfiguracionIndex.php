<?php

namespace App\Livewire\Configuracion;

use Livewire\Component;
use Livewire\Attributes\Layout;

class ConfiguracionIndex extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.configuracion.configuracion-index');
    }
}
