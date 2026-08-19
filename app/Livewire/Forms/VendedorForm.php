<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Form;

class VendedorForm extends Form
{
    public ?Vendedor $vendedor = null;

    public string $nombre = '';

    public string $email = '';

    public string $password = '';

    public string $rol = '';

    public ?string $codigo = null;

    public ?string $comision_porcentaje = null;

    public ?string $descuento_maximo_porcentaje = '0';

    public bool $activo = true;

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->vendedor?->user_id),
            ],
            'password' => [
                $this->vendedor ? 'nullable' : 'required',
                'string',
                'min:8',
            ],
            'rol' => ['required', 'string', Rule::exists('roles', 'name')],
            'codigo' => ['nullable', 'string', 'max:50'],
            'comision_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'descuento_maximo_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'activo' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.required' => 'Ingresa un correo válido.',
            'email.email' => 'Ingresa un correo válido.',
            'email.unique' => 'Este correo ya está en uso.',
            'password.required' => 'La contraseña inicial es obligatoria.',
            'password.min' => 'Mínimo 8 caracteres.',
            'rol.required' => 'Selecciona un rol.',
            'comision_porcentaje.numeric' => 'El valor debe estar entre 0 y 100.',
            'comision_porcentaje.min' => 'El valor debe estar entre 0 y 100.',
            'comision_porcentaje.max' => 'El valor debe estar entre 0 y 100.',
            'descuento_maximo_porcentaje.numeric' => 'El valor debe estar entre 0 y 100.',
            'descuento_maximo_porcentaje.min' => 'El valor debe estar entre 0 y 100.',
            'descuento_maximo_porcentaje.max' => 'El valor debe estar entre 0 y 100.',
        ];
    }

    public function setVendedor(Vendedor $vendedor)
    {
        $this->vendedor = $vendedor;
        $this->nombre = $vendedor->user->name;
        $this->email = $vendedor->user->email;
        $this->rol = $vendedor->user->roles->first()?->name ?? '';
        $this->codigo = $vendedor->codigo;
        $this->comision_porcentaje = (string) $vendedor->comision_porcentaje;
        $this->descuento_maximo_porcentaje = (string) $vendedor->descuento_maximo_porcentaje;
        $this->activo = $vendedor->activo;
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            $user = User::create([
                'name' => trim($this->nombre),
                'email' => trim($this->email),
                'password' => Hash::make($this->password),
            ]);

            $user->assignRole($this->rol);

            Vendedor::create([
                'user_id' => $user->id,
                'codigo' => $this->codigo ? trim($this->codigo) : null,
                'comision_porcentaje' => $this->comision_porcentaje !== '' ? $this->comision_porcentaje : null,
                'descuento_maximo_porcentaje' => $this->descuento_maximo_porcentaje !== '' ? $this->descuento_maximo_porcentaje : 0,
                'activo' => $this->activo,
            ]);
        });

        $this->reset();
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            $user = $this->vendedor->user;

            $user->name = trim($this->nombre);
            $user->email = trim($this->email);

            if (! empty($this->password)) {
                $user->password = Hash::make($this->password);
            }

            $user->save();
            $user->syncRoles([$this->rol]);

            $this->vendedor->update([
                'codigo' => $this->codigo ? trim($this->codigo) : null,
                'comision_porcentaje' => $this->comision_porcentaje !== '' ? $this->comision_porcentaje : null,
                'descuento_maximo_porcentaje' => $this->descuento_maximo_porcentaje !== '' ? $this->descuento_maximo_porcentaje : 0,
                'activo' => $this->activo,
            ]);
        });
    }
}
