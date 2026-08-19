<?php

namespace App\Livewire\Vendedores;

use App\Livewire\Forms\VendedorForm as VendedorFormObject;
use App\Models\Vendedor;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class VendedorForm extends Component
{
    public VendedorFormObject $form;

    public ?Vendedor $vendedor = null;

    public bool $isEdit = false;

    public $roles = [];

    public function mount(?Vendedor $vendedor = null)
    {
        $this->roles = Role::all();

        if (! $vendedor?->exists) {
            return;
        }

        $this->vendedor = $vendedor;
        $this->isEdit = true;
        $this->form->setVendedor($vendedor);
    }

    public function save()
    {
        $this->authorize('vendedores.gestionar');

        if ($this->isEdit) {
            $this->form->update();
            session()->flash('success', 'Cambios guardados.');
        } else {
            $this->form->store();
            session()->flash('success', 'Vendedor creado correctamente.');
        }

        return $this->redirect(route('vendedores'), navigate: true);
    }

    public function render()
    {
        return view('livewire.vendedores.vendedor-form');
    }
}
