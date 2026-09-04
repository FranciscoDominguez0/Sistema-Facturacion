<?php

namespace App\Livewire\Roles;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Livewire\Forms\UsuarioForm;

class UsuarioIndex extends Component
{
    use WithPagination;

    public UsuarioForm $form;
    public $search = '';
    public $filtroRol = '';
    public $modalVisible = false;
    public $modalEliminarVisible = false;
    public $usuarioAEliminarId = null;
    public $tituloModal = 'Nuevo Usuario';

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

    public function abrirModal()
    {
        $this->form->reset();
        $this->form->usuario = null;
        $this->tituloModal = 'Nuevo Usuario';
        $this->modalVisible = true;
    }

    public function editarUsuario($id)
    {
        $usuario = User::findOrFail($id);
        $this->form->setUsuario($usuario);
        $this->tituloModal = 'Editar Usuario';
        $this->modalVisible = true;
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

    public function guardarUsuario()
    {
        $this->authorize($this->form->usuario ? 'usuarios.editar' : 'usuarios.crear');

        $this->form->guardar();
        $this->modalVisible = false;
        $this->dispatch('toast', message: $this->form->usuario ? 'Usuario actualizado exitosamente.' : 'Usuario creado exitosamente.', type: 'success');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filtroRol) {
            $query->role($this->filtroRol);
        }

        $usuarios = $query->latest()->paginate(10);
        $roles = Role::all();

        return view('livewire.roles.usuario-index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
        ]);
    }
}
