<?php

namespace App\Livewire\Configuracion;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EmpresaForm extends Component
{
    public function render()
    {
        return view('livewire.configuracion.empresa-form');
    }
}
