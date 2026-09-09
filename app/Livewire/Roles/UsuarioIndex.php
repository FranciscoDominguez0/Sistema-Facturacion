<?php

namespace App\Livewire\Roles;

use App\Livewire\Forms\UsuarioForm;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UsuarioIndex extends Component
{
    use WithPagination;

    public UsuarioForm $form;

    public $search = '';

    public $filtroRol = '';

    public $filtroEstado = '';

    public $view = 'list';

    public $tab = 'detalles';

    public $modalEliminarVisible = false;

    public $modalEliminarMasivoVisible = false;

    public $usuarioAEliminarId = null;

    public $seleccionados = [];

    public function mount()
    {
        $this->authorize('usuarios.ver');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroRol()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function crearUsuario()
    {
        $this->form->reset();
        $this->form->usuario = null;
        $this->view = 'form';
        $this->tab = 'detalles';
    }

    public function editarUsuario($id)
    {
        $usuario = User::findOrFail($id);
        $this->form->setUsuario($usuario);
        $this->view = 'form';
        $this->tab = 'detalles';
    }

    public function volverAtras()
    {
        $this->view = 'list';
        $this->form->reset();
    }

    public function confirmarEliminacion($id)
    {
        $this->usuarioAEliminarId = $id;
        $this->modalEliminarVisible = true;
    }

    public function eliminarUsuario()
    {
        $this->authorize('usuarios.eliminar');

        if ($this->usuarioAEliminarId && $this->usuarioAEliminarId !== auth()->id()) {
            User::findOrFail($this->usuarioAEliminarId)->delete();
            $this->dispatch('toast', message: 'Usuario eliminado exitosamente.', type: 'success');
        } else {
            $this->dispatch('toast', message: 'No puedes eliminarte a ti mismo.', type: 'error');
        }

        $this->modalEliminarVisible = false;
        $this->usuarioAEliminarId = null;
    }

    public function seleccionarTodos()
    {
        $ids = $this->consultaUsuarios()->orderBy('id')->pluck('id')->all();
        $todosSeleccionados = count(array_diff($ids, $this->seleccionados)) === 0;

        $this->seleccionados = $todosSeleccionados
            ? array_values(array_diff($this->seleccionados, $ids))
            : array_values(array_unique(array_merge($this->seleccionados, $ids)));
    }

    public function confirmarEliminacionMasiva()
    {
        $this->modalEliminarMasivoVisible = true;
    }

    public function eliminarSeleccionados()
    {
        $this->authorize('usuarios.eliminar');

        $ids = array_filter($this->seleccionados, fn ($id) => (int) $id !== auth()->id());
        $cantidad = User::whereIn('id', $ids)->delete();

        $this->seleccionados = [];
        $this->modalEliminarMasivoVisible = false;

        $this->dispatch('toast', message: $cantidad ? "Se eliminaron {$cantidad} usuario(s)." : 'No puedes eliminarte a ti mismo.', type: $cantidad ? 'success' : 'error');
    }

    public function guardarUsuario()
    {
        $esEdicion = $this->form->usuario !== null;
        $this->authorize($esEdicion ? 'usuarios.editar' : 'usuarios.crear');

        $this->form->guardar();
        $this->view = 'list';
        $this->dispatch('toast', message: $esEdicion ? 'Usuario actualizado exitosamente.' : 'Usuario creado exitosamente.', type: 'success');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $usuarios = $this->consultaUsuarios()->latest()->paginate(config('paginacion.por_pagina'));
        $roles = Role::all();

        return view('livewire.roles.usuario-index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'idsPagina' => $usuarios->pluck('id')->all(),
        ]);
    }

    private function consultaUsuarios()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filtroRol) {
            $query->role($this->filtroRol);
        }

        if ($this->filtroEstado === 'Activo') {
            $query->where('activo', true);
        } elseif ($this->filtroEstado === 'Inactivo') {
            $query->where('activo', false);
        }

        return $query;
    }
}
