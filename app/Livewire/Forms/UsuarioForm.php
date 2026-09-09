<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Form;

class UsuarioForm extends Form
{
    public ?User $usuario = null;

    public string $name = '';

    public ?string $email = null;

    public string $password = '';

    public string $rol = '';

    /** @var array<int, string> */
    public array $permisos = [];

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email'.($this->usuario ? ','.$this->usuario->id : ''),
            'password' => 'nullable|string|min:8',
            'rol' => 'nullable|exists:roles,name',
            'permisos' => 'array',
            'permisos.*' => 'string',
        ];
    }

    public function setUsuario(User $usuario)
    {
        $this->usuario = $usuario;
        $this->name = $usuario->name;
        $this->email = $usuario->email;
        // Asume un solo rol principal para simplificar
        $this->rol = $usuario->roles->first()?->name ?? '';
        $this->permisos = $usuario->permissions->pluck('name')->toArray();
    }

    public function guardar()
    {
        $this->normalizar();
        $this->validate();

        $usuario = $this->usuario
            ? tap($this->usuario)->update($this->datos())
            : User::create($this->datos());

        $usuario->syncRoles($this->rol ? [$this->rol] : []);
        $usuario->syncPermissions($this->permisos);

        $this->reset();
    }

    /**
     * Limpia los campos antes de validar: sin espacios al inicio/final
     * y email en minúsculas (o null si está vacío).
     */
    private function normalizar(): void
    {
        $this->name = trim($this->name);
        $this->email = $this->email ? strtolower(trim($this->email)) : null;
    }

    /**
     * Datos que se guardan, compartidos entre alta y edición.
     */
    private function datos(): array
    {
        $datos = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $datos['password'] = Hash::make($this->password);
        }

        return $datos;
    }
}
