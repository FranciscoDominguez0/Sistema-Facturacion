<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Factura;
use App\Models\Empresa;

class GenerarPdfTest extends TestCase
{
    public function test_generar_y_guardar_pdf()
    {
        $factura = Factura::first();
        $empresa = Empresa::first();
        
        if(!$factura) {
            $this->markTestSkipped('No hay facturas');
        }

        $service = new \App\Services\FacturaPdfService();
        $pdf = $service->generarPdfBinario($factura, $empresa);
        
        file_put_contents(storage_path('app/public/test-factura.pdf'), $pdf);
        $this->assertTrue(file_exists(storage_path('app/public/test-factura.pdf')));
    }
}
