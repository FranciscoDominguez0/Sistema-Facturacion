<?php

namespace App\Livewire\Configuracion;

use App\Livewire\Forms\ImpuestoForm;
use App\Models\Impuesto;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ImpuestoIndex extends Component
{
    use WithPagination;

    public ImpuestoForm $form;

    public $view = 'list';

    public $editando = false;

    public $search = '';

    public $seleccionados = [];

    public $modalEliminarMasivoVisible = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->form->reset();
        $this->editando = false;
        $this->view = 'form';
    }

    public function edit(Impuesto $impuesto)
    {
        $this->form->resetValidation();
        $this->form->setImpuesto($impuesto);
        $this->editando = true;
        $this->view = 'form';
    }

    public function save()
    {
        if ($this->editando) {
            $this->form->update();
            $this->dispatch('toast', message: 'Impuesto actualizado con éxito.', type: 'success');
        } else {
            $this->form->store();
            $this->dispatch('toast', message: 'Impuesto creado con éxito.', type: 'success');
        }

        $this->view = 'list';
    }

    public function cancelar()
    {
        $this->view = 'list';
    }

    public function toggleActivo(Impuesto $impuesto)
    {
        $impuesto->update(['activo' => ! $impuesto->activo]);
        $this->dispatch('toast', message: 'Estado del impuesto actualizado.', type: 'success');
    }

    public function getIdsPaginaProperty()
    {
        return Impuesto::where('nombre', 'ilike', '%'.$this->search.'%')
            ->pluck('id')
            ->toArray();
    }

    public function seleccionarTodos()
    {
        $ids = $this->idsPagina;
        if (count(array_diff($ids, $this->seleccionados)) === 0) {
            $this->seleccionados = array_diff($this->seleccionados, $ids);
        } else {
            $this->seleccionados = array_unique(array_merge($this->seleccionados, $ids));
        }
    }

    public function confirmarEliminacionMasiva()
    {
        if (count($this->seleccionados) > 0) {
            $this->modalEliminarMasivoVisible = true;
        }
    }

    public function eliminarMasivo()
    {
        $cantidad = Impuesto::whereIn('id', $this->seleccionados)->delete();
        $this->seleccionados = [];
        $this->modalEliminarMasivoVisible = false;
        $this->dispatch('toast', message: "Se eliminaron {$cantidad} impuesto(s).", type: 'success');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $impuestos = Impuesto::where('nombre', 'ilike', '%'.$this->search.'%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.configuracion.impuesto-index', [
            'impuestos' => $impuestos,
        ]);
    }
}
