<?php

namespace App\Livewire\Configuracion;

use App\Models\Empresa;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmpresaForm extends Component
{
    use WithFileUploads;

    public string $tabActiva = 'detalles';

    // Propiedades del formulario
    public $nombre;
    public $identificacion_fiscal;
    public $moneda;
    public $simbolo_moneda;
    public $impuesto_nombre;
    public $impuesto_porcentaje;
    public $color_primario;
    public $pie_pagina_pdf;

    public $logo; // Para el nuevo archivo subido
    public $logo_path_actual; // Para mostrar el logo actual

    public function mount(string $tab = 'detalles')
    {
        $this->authorize('empresa.gestionar');
        $this->tabActiva = $tab;

        $empresa = Empresa::actual();
        
        $this->nombre = $empresa->nombre;
        $this->identificacion_fiscal = $empresa->identificacion_fiscal;
        $this->moneda = $empresa->moneda;
        $this->simbolo_moneda = $empresa->simbolo_moneda;
        $this->impuesto_nombre = $empresa->impuesto_nombre;
        $this->impuesto_porcentaje = $empresa->impuesto_porcentaje;
        $this->color_primario = $empresa->color_primario ?? '#0f172a';
        $this->pie_pagina_pdf = $empresa->pie_pagina_pdf;
        
        $this->logo_path_actual = $empresa->logo_path;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'identificacion_fiscal' => 'nullable|string|max:255',
            'moneda' => 'required|string|max:50',
            'simbolo_moneda' => 'required|string|max:10',
            'impuesto_nombre' => 'required|string|max:20',
            'impuesto_porcentaje' => 'required|numeric|min:0|max:100',
            'color_primario' => ['nullable', 'string', 'max:7', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'pie_pagina_pdf' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:2048', // Max 2MB
        ];
    }

    public function getImpuestoChipPreviewProperty()
    {
        $nombre = $this->impuesto_nombre ?: 'IMP';
        return $nombre . ' ' . (float)$this->impuesto_porcentaje . '%';
    }

    public function guardar()
    {
        $this->authorize('empresa.gestionar');
        $this->validate();

        $empresa = Empresa::actual();

        $path = $empresa->logo_path;
        
        if ($this->logo) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $path = $this->logo->store('logos', 'public');
        }

        $empresa->update([
            'nombre' => $this->nombre,
            'identificacion_fiscal' => $this->identificacion_fiscal,
            'moneda' => $this->moneda,
            'simbolo_moneda' => $this->simbolo_moneda,
            'impuesto_nombre' => $this->impuesto_nombre,
            'impuesto_porcentaje' => $this->impuesto_porcentaje,
            'color_primario' => $this->color_primario,
            'pie_pagina_pdf' => $this->pie_pagina_pdf,
            'logo_path' => $path,
        ]);

        $this->logo_path_actual = $path;
        $this->logo = null; // Reset the uploaded file instance

        $this->dispatch('toast', message: 'Configuración actualizada con éxito.', type: 'success');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.configuracion.empresa-form');
    }
}
