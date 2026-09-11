<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Services\FacturaPdfService;

class PdfFacturaController extends Controller
{
    /**
     * Genera el PDF de la factura.
     * Con ?print=true lo muestra en el navegador (stream); sin él, lo descarga.
     */
    public function mostrar(Factura $factura, FacturaPdfService $pdfService)
    {
        $nombreArchivo = 'factura-'.$factura->numero_factura.'.pdf';

        if (request()->has('print')) {
            $html = $pdfService->renderizarHtml($factura);

            return response($html)->header('Content-Type', 'text/html');
        }

        $pdfBinary = $pdfService->generarPdfBinario($factura);

        // attachment fuerza la descarga: el navegador no abre el PDF
        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$nombreArchivo.'"',
        ]);
    }

    /**
     * Muestra la vista HTML directamente para propósitos de desarrollo y vista previa.
     */
    public function preview(Factura $factura, FacturaPdfService $pdfService)
    {
        $html = $pdfService->renderizarHtml($factura);

        return response($html)->header('Content-Type', 'text/html');
    }
}
