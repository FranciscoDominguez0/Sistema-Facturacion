<?php

namespace App\Livewire\Clientes;

use App\Livewire\Forms\ClienteForm as ClienteFormObject;
use App\Models\Cliente;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ClienteForm extends Component
{
    public ClienteFormObject $form;

    public function mount(?Cliente $cliente = null)
    {
        if ($cliente && $cliente->exists) {
            $this->form->setCliente($cliente);
        }
    }

    public function save()
    {
        if ($this->form->cliente) {
            $this->form->update();
            session()->flash('success', 'El registro ha sido actualizado exitosamente.');
        } else {
            $this->form->store();
            session()->flash('success', 'El registro ha sido creado exitosamente.');
        }

        // navigate: solo se actualiza el contenido, el sidebar no se recarga
        return $this->redirectRoute('clientes', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.clientes.cliente-form');
    }
}
