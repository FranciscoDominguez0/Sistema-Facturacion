<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolIndex extends Component
{
    public $rolActivoId = null;
    public $modalRolVisible = false;
    public $nuevoRolNombre = '';

    public function mount()
    {
        $primerRol = Role::first();
        if ($primerRol) {
            $this->rolActivoId = $primerRol->id;
        }
    }

    public function seleccionarRol($id)
    {
        $this->rolActivoId = $id;
    }

    public function abrirModalRol()
    {
        $this->nuevoRolNombre = '';
        $this->modalRolVisible = true;
    }

    public function guardarRol()
    {
        $this->validate([
            'nuevoRolNombre' => 'required|string|max:255|unique:roles,name'
        ]);

        $nuevoRol = Role::create(['name' => $this->nuevoRolNombre]);
        $this->modalRolVisible = false;
        
        // Seleccionamos automáticamente el rol recién creado
        $this->rolActivoId = $nuevoRol->id;
        
        $this->dispatch('toast', message: 'Rol creado exitosamente.', type: 'success');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $roles = Role::withCount('users')->get();
        $rolActivo = Role::with('permissions')->find($this->rolActivoId);
        $permisosAgrupados = Permission::all()->groupBy(function($permiso) {
            // Suponiendo que el nombre del permiso es "ver facturas" podemos agruparlo, 
            // o si el sistema tiene módulos definidos, podemos agrupar por un campo extra.
            // Para mantenerlo simple, usaremos un prefijo o la primera palabra.
            $partes = explode(' ', $permiso->name);
            return $partes[1] ?? 'General'; 
        });

        return view('livewire.roles.rol-index', compact('roles', 'rolActivo', 'permisosAgrupados'));
    }
}
