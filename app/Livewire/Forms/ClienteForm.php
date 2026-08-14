<?php

namespace App\Livewire\Forms;

use App\Models\Cliente;
use Livewire\Form;

class ClienteForm extends Form
{
    public ?Cliente $cliente = null;

    public string $nombre = '';

    public string $identificacion = '';

    public string $email = '';

    public string $telefono = '';

    public string $direccion = '';

    public bool $activo = true;

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

    /**
     * Reglas compartidas entre alta y edición (evitan duplicación).
     */
    protected function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'identificacion' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string'],
            'activo' => ['boolean'],
        ];
    }

    public function store()
    {
        $this->normalizar();
        $this->validate();

        Cliente::create($this->datos());

        $this->reset();
    }

    public function update()
    {
        $this->normalizar();
        $this->validate();

        $this->cliente->update($this->datos());
    }

    /**
     * Limpia los campos antes de validar: sin espacios al inicio/final
     * y email siempre en minúsculas.
     */
    protected function normalizar(): void
    {
        $this->nombre = trim($this->nombre);
        $this->identificacion = trim($this->identificacion);
        $this->email = strtolower(trim($this->email));
        $this->telefono = trim($this->telefono);
        $this->direccion = trim($this->direccion);
    }

    /**
     * Atributos que se guardan, compartidos entre alta y edición.
     */
    protected function datos(): array
    {
        return [
            'nombre' => $this->nombre,
            'identificacion' => $this->identificacion,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'activo' => $this->activo,
        ];
    }
}
