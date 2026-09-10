<?php

namespace App\Livewire\Configuracion;

use App\Livewire\Forms\EmpresaForm as EmpresaFormData;
use App\Models\Empresa;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmpresaForm extends Component
{
    use WithFileUploads;

    public EmpresaFormData $form;

    // El archivo del logo vive en el componente: los uploads de Livewire
    // no se soportan dentro de form objects.
    public $logo;

    // Ruta del logo guardado, solo para mostrarlo en la vista previa.
    public $logo_path_actual;

    public function mount()
    {
        $this->authorize('empresa.gestionar');

        $empresa = Empresa::actual();
        $this->form->setEmpresa($empresa);
        $this->logo_path_actual = $empresa->logo_path;
    }



    public function guardar()
    {
        $this->authorize('empresa.gestionar');
        $this->validate(['logo' => 'nullable|image|max:2048']);

        $rutaLogo = $this->logo
            ? $this->guardarNuevoLogo()
            : $this->logo_path_actual;

        $this->form->guardar($rutaLogo);

        $this->logo_path_actual = $rutaLogo;
        $this->logo = null; // limpia el archivo subido para volver a mostrar el logo actual

        $this->dispatch('toast', message: 'Configuración actualizada con éxito.', type: 'success');
    }

    /**
     * Reemplaza el logo anterior (si existe) y guarda el nuevo en disco.
     */
    private function guardarNuevoLogo(): string
    {
        if ($this->logo_path_actual && Storage::disk('public')->exists($this->logo_path_actual)) {
            Storage::disk('public')->delete($this->logo_path_actual);
        }

        return $this->logo->store('logos', 'public');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.configuracion.empresa-form');
    }
}
