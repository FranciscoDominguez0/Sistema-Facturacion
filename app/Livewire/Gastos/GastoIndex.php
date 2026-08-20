<?php

namespace App\Livewire\Gastos;

use App\Models\Gasto;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class GastoIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updated($property)
    {
        if ($property === 'search') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $gastos = Gasto::with('registradoPor')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('concepto', 'ilike', '%'.$this->escapeLike($this->search).'%')
                        ->orWhere('categoria', 'ilike', '%'.$this->escapeLike($this->search).'%');
                });
            })
            ->recientes()
            ->paginate(15);

        return view('livewire.gastos.gasto-index', [
            'gastos' => $gastos,
        ]);
    }

    protected function escapeLike(string $termino): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $termino);
    }
}
