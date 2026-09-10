<?php

namespace App\Livewire\Forms;

use App\Models\Impuesto;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ImpuestoForm extends Form
{
    public ?Impuesto $impuesto = null;

    #[Validate('required|string|max:255')]
    public $nombre = '';

    #[Validate('required|numeric|min:0|max:100')]
    public $porcentaje = '';

    public $activo = true;

    public function setImpuesto(Impuesto $impuesto)
    {
        $this->impuesto = $impuesto;
        $this->nombre = $impuesto->nombre;
        $this->porcentaje = $impuesto->porcentaje;
        $this->activo = $impuesto->activo;
    }

    public function store()
    {
        $this->validate();

        Impuesto::create([
            'nombre' => $this->nombre,
            'porcentaje' => $this->porcentaje,
            'activo' => $this->activo,
        ]);

        $this->reset();
    }

    public function update()
    {
        $this->validate();

        $this->impuesto->update([
            'nombre' => $this->nombre,
            'porcentaje' => $this->porcentaje,
            'activo' => $this->activo,
        ]);

        $this->reset();
    }
}
