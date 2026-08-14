<?php

namespace App\Livewire\Forms;

use App\Models\Cliente;
use Livewire\Form;

class ClienteForm extends Form
{
    public ?Cliente $cliente = null;

    public $nombre = '';

    public $identificacion = '';

    public $email = '';

    public $telefono = '';

    public $direccion = '';

    public $activo = true;

    public function setCliente(Cliente $cliente)
    {
        $this->cliente = $cliente;

        $this->nombre = $cliente->nombre;
        $this->identificacion = $cliente->identificacion;
        $this->email = $cliente->email;
        $this->telefono = $cliente->telefono;
        $this->direccion = $cliente->direccion;
        $this->activo = $cliente->activo;
    }

    public function store()
    {
        $this->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'identificacion' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string'],
            'activo' => ['boolean'],
        ]);

        Cliente::create([
            'nombre' => $this->nombre,
            'identificacion' => $this->identificacion,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'activo' => $this->activo,
        ]);

        $this->reset();
    }

    public function update()
    {
        $this->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'identificacion' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string'],
            'activo' => ['boolean'],
        ]);

        $this->cliente->update([
            'nombre' => $this->nombre,
            'identificacion' => $this->identificacion,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'activo' => $this->activo,
        ]);
    }
}
