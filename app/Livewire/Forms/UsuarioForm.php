<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UsuarioForm extends Form
{
    public ?User $usuario = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    public $email = '';

    #[Validate('required|string|min:8')]
    public $password = '';

    #[Validate('required|exists:roles,name')]
    public $rol = '';

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email' . ($this->usuario ? ',' . $this->usuario->id : ''),
            'password' => $this->usuario ? 'nullable|string|min:8' : 'required|string|min:8',
            'rol' => 'required|exists:roles,name',
        ];
    }

    public function setUsuario(User $usuario)
    {
        $this->usuario = $usuario;
        $this->name = $usuario->name;
        $this->email = $usuario->email;
        // Asume un solo rol principal para simplificar
        $this->rol = $usuario->roles->first()?->name ?? '';
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
        }

        $this->reset();
    }
}
