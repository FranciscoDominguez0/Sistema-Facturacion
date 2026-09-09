<?php

namespace App\Livewire\Forms;

use App\Models\Empresa;
use Livewire\Form;

/**
 * Datos de la empresa (configuración básica): validación y guardado.
 * El logo se sube aparte desde el componente porque Livewire solo
 * admite archivos en propiedades del componente, no del form object.
 */
class EmpresaForm extends Form
{
    public string $nombre = '';

    public ?string $identificacion_fiscal = null;

    public string $moneda = '';

    public string $simbolo_moneda = '';

    public string $impuesto_nombre = '';

    public $impuesto_porcentaje = 0;

    public string $color_primario = '#0f172a';

    public ?string $pie_pagina_pdf = null;

    public function setEmpresa(Empresa $empresa): void
    {
        $this->nombre = $empresa->nombre;
        $this->identificacion_fiscal = $empresa->identificacion_fiscal;
        $this->moneda = $empresa->moneda;
        $this->simbolo_moneda = $empresa->simbolo_moneda;
        $this->impuesto_nombre = $empresa->impuesto_nombre ?? '';
        $this->impuesto_porcentaje = $empresa->impuesto_porcentaje;
        $this->color_primario = $empresa->color_primario ?? '#0f172a';
        $this->pie_pagina_pdf = $empresa->pie_pagina_pdf;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'identificacion_fiscal' => ['nullable', 'string', 'max:255'],
            'moneda' => ['required', 'string', 'max:50'],
            'simbolo_moneda' => ['required', 'string', 'max:10'],
            'impuesto_nombre' => ['required', 'string', 'max:20'],
            'impuesto_porcentaje' => ['required', 'numeric', 'min:0', 'max:100'],
            'color_primario' => ['nullable', 'string', 'max:7', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'pie_pagina_pdf' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Texto de la insignia de vista previa del impuesto (ej. "IVA 21%").
     */
    public function vistaPreviaImpuesto(): string
    {
        $nombre = $this->impuesto_nombre ?: 'IMP';

        return $nombre.' '.(float) $this->impuesto_porcentaje.'%';
    }

    public function guardar(?string $rutaLogo): void
    {
        $this->normalizar();
        $this->validate();

        Empresa::actual()->update([
            'nombre' => $this->nombre,
            'identificacion_fiscal' => $this->identificacion_fiscal,
            'moneda' => $this->moneda,
            'simbolo_moneda' => $this->simbolo_moneda,
            'impuesto_nombre' => $this->impuesto_nombre,
            'impuesto_porcentaje' => $this->impuesto_porcentaje,
            'color_primario' => $this->color_primario,
            'pie_pagina_pdf' => $this->pie_pagina_pdf,
            'logo_path' => $rutaLogo,
        ]);
    }

    /**
     * Limpia campos de texto antes de validar.
     */
    private function normalizar(): void
    {
        $this->nombre = trim($this->nombre);
        $this->identificacion_fiscal = $this->identificacion_fiscal ? trim($this->identificacion_fiscal) : null;
        $this->pie_pagina_pdf = $this->pie_pagina_pdf ? trim($this->pie_pagina_pdf) : null;
    }
}
