<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Facturas\FacturaPdf;
use App\Mail\FacturaMail;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\User;
use App\Services\FacturaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Tests de la vista previa del PDF (FacturaPdf) y de la descarga del PDF.
 *
 * El proyecto usa PHPUnit clásico con nombres de test en español.
 */
class FacturaPdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('facturas.ver');
        Permission::findOrCreate('facturas.editar');
        Permission::findOrCreate('facturas.eliminar');
    }

    // =====================================================================
    // Vista previa
    // =====================================================================

    /**
     * Un usuario con permiso puede ver la vista previa del PDF de la factura.
     */
    public function test_un_usuario_con_permiso_puede_ver_la_vista_previa(): void
    {
        $factura = $this->crearFacturaReal();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.ver');

        Livewire::actingAs($usuario)
            ->test(FacturaPdf::class, ['factura' => $factura])
            ->assertStatus(200)
            ->assertSee($factura->numero_factura);
    }

    // =====================================================================
    // Enviar por correo
    // =====================================================================

    /**
     * Enviar la factura por correo usa el email del cliente y adjunta el PDF.
     */
    public function test_enviar_por_correo_manda_la_factura_al_cliente(): void
    {
        Mail::fake();

        $factura = $this->crearFacturaReal();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.ver');

        Livewire::actingAs($usuario)
            ->test(FacturaPdf::class, ['factura' => $factura])
            ->call('enviarPorCorreo')
            ->assertDispatched('toast');

        Mail::assertSent(FacturaMail::class, function (FacturaMail $mail) use ($factura) {
            return $mail->factura->id === $factura->id
                && $mail->hasTo($factura->cliente->email);
        });
    }

    /**
     * Si el cliente no tiene correo, no se envía nada y se avisa con un toast.
     */
    public function test_enviar_por_correo_sin_email_del_cliente_avisa_con_error(): void
    {
        Mail::fake();

        $factura = $this->crearFacturaReal();
        $factura->cliente->update(['email' => null]);
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.ver');

        Livewire::actingAs($usuario)
            ->test(FacturaPdf::class, ['factura' => $factura])
            ->call('enviarPorCorreo')
            ->assertDispatched('toast', type: 'error');

        Mail::assertNothingSent();
    }

    // =====================================================================
    // Descarga del PDF
    // =====================================================================

    /**
     * El PDF generado es válido (content-type application/pdf y contenido
     * binario de PDF).
     */
    public function test_descargar_pdf_genera_un_pdf_valido(): void
    {
        $factura = $this->crearFacturaReal();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.ver');

        $respuesta = $this->actingAs($usuario)
            ->get(route('facturas.pdf', $factura));

        $respuesta->assertOk();
        $respuesta->assertHeader('Content-Type', 'application/pdf');

        $contenido = $respuesta->getContent();

        $this->assertStringStartsWith('%PDF', $contenido);
        $this->assertNotEmpty($contenido);
    }

    /**
     * El PDF generado no contiene términos de facturación electrónica
     * (CUFE, DGI, XML firmado, etc.).
     */
    public function test_el_pdf_no_contiene_terminos_de_facturacion_electronica(): void
    {
        $factura = $this->crearFacturaReal();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.ver');

        $respuesta = $this->actingAs($usuario)
            ->get(route('facturas.pdf', $factura));

        $respuesta->assertOk();

        $contenido = $respuesta->getContent();

        foreach (['CUFE', 'DGI', 'XML firmado', 'electrónica', 'factura electrónica'] as $termino) {
            $this->assertStringNotContainsString($termino, $contenido);
        }
    }

    // =====================================================================
    // Helpers
    // =====================================================================

    /**
     * Crea una factura real con el servicio (datos consistentes).
     */
    protected function crearFacturaReal(): Factura
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);

        $servicio = app(FacturaService::class);

        $cliente = Cliente::factory()->create(['email' => 'cliente@test.com']);

        return $servicio->crear([
            'cliente_id' => $cliente->id,
            'vendedor_id' => User::factory()->create()->id,
            'fecha_emision' => '2026-08-19',
            'fecha_vencimiento' => null,
            'descuento_porcentaje' => 0,
            'notas' => null,
            'items' => [
                [
                    'producto_id' => null,
                    'descripcion' => 'Producto A',
                    'cantidad' => 2,
                    'precio_unitario' => 100,
                    'descuento_porcentaje' => 0,
                    'impuesto_porcentaje' => 7,
                    'impuesto_nombre' => 'ITBMS',
                ],
            ],
        ]);
    }
}
