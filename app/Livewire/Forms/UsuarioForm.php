<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Form;

class UsuarioForm extends Form
{
    public ?User $usuario = null;

    public $name = '';

    public $email = '';

    public $password = '';

    public $rol = '';

    public array $permisos = [];

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email'.($this->usuario ? ','.$this->usuario->id : ''),
            'password' => 'nullable|string|min:8',
            'rol' => 'nullable|exists:roles,name',
            'permisos' => 'array',
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
        $this->validate();

        if ($this->usuario) {
            $data = [
                'name' => $this->name,
                'email' => $this->email ?: null,
            ];

            if (! empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $this->usuario->update($data);
            $user = $this->usuario;
        } else {
            $data = [
                'name' => $this->name,
                'email' => $this->email ?: null,
            ];

            if (! empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $user = User::create($data);
        }

        if ($this->rol) {
            $user->syncRoles([$this->rol]);
        } else {
            $user->syncRoles([]);
        }

        $user->syncPermissions($this->permisos);

        $this->reset();
    }
}
