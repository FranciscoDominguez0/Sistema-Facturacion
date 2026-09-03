<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Livewire\Attributes\Layout;

class RolIndex extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.roles.rol-index');
    }
}
