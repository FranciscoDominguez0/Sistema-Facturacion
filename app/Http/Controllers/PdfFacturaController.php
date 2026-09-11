<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Factura;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfFacturaController extends Controller
{
    /**
     * Genera el PDF de la factura.
     * Con ?print=true lo muestra en el navegador (stream); sin él, lo descarga.
     */
    public function mostrar(Factura $factura)
    {
        $empresa = Empresa::actual();
        $factura->load(['cliente', 'vendedor', 'items']);
        $pdf = Pdf::loadView('pdf.factura', compact('factura', 'empresa'));

        $nombreArchivo = 'factura-'.$factura->numero_factura.'.pdf';

        if (request()->has('print')) {
            return $pdf->stream($nombreArchivo);
        }

        return $pdf->download($nombreArchivo);
    }
}
