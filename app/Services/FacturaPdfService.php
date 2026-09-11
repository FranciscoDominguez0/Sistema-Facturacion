<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\Factura;
use Spatie\Browsershot\Browsershot;

class FacturaPdfService
{
    /**
     * Renderiza la vista de factura en HTML.
     */
    public function renderizarHtml(Factura $factura, ?Empresa $empresa = null): string
    {
        $empresa = $empresa ?? Empresa::actual();
        $factura->loadMissing(['cliente', 'vendedor', 'items']);

        // Renderizamos la nueva vista factura-pdf (aún por crear)
        return view('factura-pdf', compact('factura', 'empresa'))->render();
    }

    /**
     * Genera el contenido binario del PDF utilizando Browsershot.
     */
    public function generarPdfBinario(Factura $factura, ?Empresa $empresa = null): string
    {
        $html = $this->renderizarHtml($factura, $empresa);

        return Browsershot::html($html)
            ->noSandbox()
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->pdf();
    }
}
