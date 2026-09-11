<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Factura;


class PdfFacturaController extends Controller
{
    /**
     * Genera el PDF de la factura.
     * Con ?print=true lo muestra en el navegador (stream); sin él, lo descarga.
     */
    public function mostrar(Factura $factura, \App\Services\FacturaPdfService $pdfService)
    {
        $nombreArchivo = 'factura-'.$factura->numero_factura.'.pdf';

        if (request()->has('print')) {
            $html = $pdfService->renderizarHtml($factura);
            return response($html)->header('Content-Type', 'text/html');
        }

        $pdfBinary = $pdfService->generarPdfBinario($factura);

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$nombreArchivo.'"',
        ]);
    }

    /**
     * Muestra la vista HTML directamente para propósitos de desarrollo y vista previa.
     */
    public function preview(Factura $factura, \App\Services\FacturaPdfService $pdfService)
    {
        $html = $pdfService->renderizarHtml($factura);
        return response($html)->header('Content-Type', 'text/html');
    }
}
