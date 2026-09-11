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

    public ?string $email = null;

    public ?string $telefono = null;

    public ?string $ruc = null;

    public ?string $dv = null;

    public string $moneda = '';

    public string $simbolo_moneda = '';

    public string $color_primario = '#0f172a';

    public ?string $pie_pagina_pdf = null;

    public function setEmpresa(Empresa $empresa): void
    {
        $this->nombre = $empresa->nombre;
        $this->identificacion_fiscal = $empresa->identificacion_fiscal;
        $this->email = $empresa->email;
        $this->telefono = $empresa->telefono;
        $this->ruc = $empresa->ruc;
        $this->dv = $empresa->dv;
        $this->moneda = $empresa->moneda;
        $this->simbolo_moneda = $empresa->simbolo_moneda;
        $this->color_primario = $empresa->color_primario ?? '#0f172a';
        $this->pie_pagina_pdf = $empresa->pie_pagina_pdf;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'identificacion_fiscal' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'dv' => ['nullable', 'string', 'max:5'],
            'moneda' => ['required', 'string', 'max:50'],
            'simbolo_moneda' => ['required', 'string', 'max:10'],
            'color_primario' => ['nullable', 'string', 'max:7', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'pie_pagina_pdf' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function guardar(?string $rutaLogo): void
    {
        $this->normalizar();
        $this->validate();

        Empresa::actual()->update([
            'nombre' => $this->nombre,
            'identificacion_fiscal' => $this->identificacion_fiscal,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'ruc' => $this->ruc,
            'dv' => $this->dv,
            'moneda' => $this->moneda,
            'simbolo_moneda' => $this->simbolo_moneda,
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
        $this->email = $this->email ? trim($this->email) : null;
        $this->telefono = $this->telefono ? trim($this->telefono) : null;
        $this->ruc = $this->ruc ? trim($this->ruc) : null;
        $this->dv = $this->dv ? trim($this->dv) : null;
        $this->pie_pagina_pdf = $this->pie_pagina_pdf ? trim($this->pie_pagina_pdf) : null;
    }
}
