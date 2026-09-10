<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ClienteShow extends Component
{
    public Cliente $cliente;

    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.clientes.cliente-show', [
            'facturas' => $this->cliente->facturas()->latest()->limit(10)->get(),
        ]);
    }
}
