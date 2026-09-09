<?php

namespace App\Livewire\Forms;

use App\Models\Empresa;
use Livewire\Form;

/**
 * Numeración de facturas (configuración): validación y guardado.
 */
class FacturacionForm extends Form
{
    public string $prefijo_factura = '';

    public $siguiente_numero_factura = 1;

    public function setEmpresa(Empresa $empresa): void
    {
        $this->prefijo_factura = $empresa->prefijo_factura;
        $this->siguiente_numero_factura = $empresa->siguiente_numero_factura;
    }

    public function rules(): array
    {
        return [
            'prefijo_factura' => ['nullable', 'string', 'max:10'],
            'siguiente_numero_factura' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Números de la secuencia, rellenados con ceros a la izquierda.
     */
    public function numeroPreview(): string
    {
        return $this->prefijo_factura.str_pad((int) $this->siguiente_numero_factura, 6, '0', STR_PAD_LEFT);
    }

    public function numeroPreviewAnterior(): string
    {
        $numero = max(1, (int) $this->siguiente_numero_factura - 1);

        return $this->prefijo_factura.str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    public function numeroPreviewSiguiente(): string
    {
        return $this->prefijo_factura.str_pad((int) $this->siguiente_numero_factura + 1, 6, '0', STR_PAD_LEFT);
    }

    public function guardar(): void
    {
        $this->validate();

        Empresa::actual()->update([
            'prefijo_factura' => $this->prefijo_factura,
            'siguiente_numero_factura' => $this->siguiente_numero_factura,
        ]);
    }
}
