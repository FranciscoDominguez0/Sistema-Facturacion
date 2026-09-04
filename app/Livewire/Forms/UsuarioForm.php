<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
            'email' => 'required|string|email|max:255|unique:users,email' . ($this->usuario ? ',' . $this->usuario->id : ''),
            'password' => $this->usuario ? 'nullable|string|min:8' : 'required|string|min:8',
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
                'email' => $this->email,
            ];
            
            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            
            $this->usuario->update($data);
            $user = $this->usuario;
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
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
