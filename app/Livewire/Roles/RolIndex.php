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
    public $permisosAsignados = [];

    public function mount()
    {
        $this->authorize('empresa.gestionar');
        
        $primerRol = Role::first();
        if ($primerRol) {
            $this->seleccionarRol($primerRol->id);
        }
    }

    public function seleccionarRol($id)
    {
        $this->rolActivoId = $id;
        $rol = Role::with('permissions')->find($id);
        if ($rol) {
            $this->permisosAsignados = $rol->permissions->pluck('name')->toArray();
        } else {
            $this->permisosAsignados = [];
        }
    }

    public function abrirModalRol()
    {
        $this->nuevoRolNombre = '';
        $this->modalRolVisible = true;
    }

    public function guardarRol()
    {
        $this->authorize('empresa.gestionar');

        $this->validate([
            'nuevoRolNombre' => 'required|string|max:255|unique:roles,name'
        ]);

        $nuevoRol = Role::create(['name' => $this->nuevoRolNombre]);
        $this->modalRolVisible = false;
        
        // Seleccionamos automáticamente el rol recién creado
        $this->seleccionarRol($nuevoRol->id);
        
        $this->dispatch('toast', message: 'Rol creado exitosamente.', type: 'success');
    }

    public function guardarPermisos()
    {
        $this->authorize('empresa.gestionar');

        if (!$this->rolActivoId) return;

        $rol = Role::find($this->rolActivoId);
        if ($rol) {
            $rol->syncPermissions($this->permisosAsignados);
            $this->dispatch('toast', message: 'Permisos actualizados correctamente.', type: 'success');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $roles = Role::withCount('users')->get();
        $rolActivo = Role::with('permissions')->find($this->rolActivoId);
        $permisosAgrupados = Permission::all()->groupBy(function($permiso) {
            // Separa por punto si usa notación dot, o espacio. Tomamos la primera palabra como módulo.
            $partes = preg_split('/[\s.]+/', $permiso->name);
            return $partes[0] ?? 'General'; 
        });

        return view('livewire.roles.rol-index', compact('roles', 'rolActivo', 'permisosAgrupados'));
    }
}
