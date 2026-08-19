<?php

namespace Tests\Feature\Livewire;

use App\Enums\EstadoFactura;
use App\Livewire\Facturas\FacturaShow;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\User;
use App\Models\Vendedor;
use App\Services\FacturaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Tests del detalle de factura (FacturaShow) y de la descarga del PDF.
 *
 * El proyecto usa PHPUnit clásico con nombres de test en español.
 */
class FacturaShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('facturas.ver');
        Permission::findOrCreate('facturas.estado.cambiar');
    }

    // =====================================================================
    // Detalle de la factura
    // =====================================================================

    /**
     * Un usuario con permiso puede ver el detalle de una factura existente.
     */
    public function test_un_usuario_con_permiso_puede_ver_el_detalle(): void
    {
        $factura = $this->crearFacturaReal();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.ver');

        Livewire::actingAs($usuario)
            ->test(FacturaShow::class, ['factura' => $factura])
            ->assertSee($factura->numero_factura);
    }

    /**
     * El detalle muestra número, estado, cliente, vendedor, líneas, subtotal,
     * descuento, impuesto y total.
     */
    public function test_el_detalle_muestra_los_datos_de_la_factura(): void
    {
        $factura = $this->crearFacturaReal(descuentoPorcentaje: 10);
        $factura->load(['cliente', 'vendedor.user', 'items']);
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.ver');

        // 2 x 100 = 200 subtotal; desc. global 10% = 20; impuesto 7% sobre 180 = 12.60; total 192.60
        Livewire::actingAs($usuario)
            ->test(FacturaShow::class, ['factura' => $factura])
            ->assertSee($factura->numero_factura)
            ->assertSee('Pendiente')
            ->assertSee($factura->cliente->nombre)
            ->assertSee($factura->vendedor->user->name)
            ->assertSee('Producto A')
            ->assertSee('$200.00')
            ->assertSee('10.00%')
            ->assertSee('-$20.00')
            ->assertSee('$12.60')
            ->assertSee('$192.60');
    }

    /**
     * El badge de estado refleja el color y la etiqueta correspondiente a
     * Pendiente, Pagada y Anulada.
     */
    public function test_el_badge_de_estado_refleja_el_color_de_cada_estado(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $pendiente = Factura::factory()->create();
        $pagada = Factura::factory()->pagada()->create();
        $anulada = Factura::factory()->anulada()->create();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.ver');

        // Pendiente: badge ámbar
        $html = Livewire::actingAs($usuario)
            ->test(FacturaShow::class, ['factura' => $pendiente])
            ->html();

        $this->assertMatchesRegularExpression('/class="[^"]*bg-amber-100[^"]*">\s*Pendiente\s*<\/span>/', $html);

        // Pagada: badge esmeralda
        $html = Livewire::actingAs($usuario)
            ->test(FacturaShow::class, ['factura' => $pagada])
            ->html();

        $this->assertMatchesRegularExpression('/class="[^"]*bg-emerald-100[^"]*">\s*Pagada\s*<\/span>/', $html);

        // Anulada: badge rojo
        $html = Livewire::actingAs($usuario)
            ->test(FacturaShow::class, ['factura' => $anulada])
            ->html();

        $this->assertMatchesRegularExpression('/class="[^"]*bg-red-100[^"]*">\s*Anulada\s*<\/span>/', $html);
    }

    // =====================================================================
    // Cambio de estado
    // =====================================================================

    /**
     * Cambiar estado desde el detalle solo funciona con el permiso
     * facturas.estado.cambiar; sin el permiso la acción es rechazada.
     */
    public function test_cambiar_estado_sin_permiso_es_rechazado(): void
    {
        $factura = Factura::factory()->create();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.ver');

        Livewire::actingAs($usuario)
            ->test(FacturaShow::class, ['factura' => $factura])
            ->call('cambiarEstado', 'Pagada')
            ->assertForbidden();

        $this->assertDatabaseHas('facturas', [
            'id' => $factura->id,
            'estado' => 'Pendiente',
        ]);
    }

    /**
     * Cambiar estado con permiso actualiza el registro y refresca la vista.
     */
    public function test_cambiar_estado_con_permiso_actualiza_y_refresca_la_vista(): void
    {
        $factura = Factura::factory()->create();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.estado.cambiar');

        Livewire::actingAs($usuario)
            ->test(FacturaShow::class, ['factura' => $factura])
            ->call('cambiarEstado', 'Pagada')
            ->assertSet('factura.estado', EstadoFactura::PAGADA)
            ->assertSee('Pagada');

        $this->assertDatabaseHas('facturas', [
            'id' => $factura->id,
            'estado' => 'Pagada',
        ]);
    }

    /**
     * No se puede cambiar el estado de una factura Anulada: la transición
     * inválida es rechazada.
     */
    public function test_no_se_puede_cambiar_el_estado_de_una_factura_anulada(): void
    {
        $factura = Factura::factory()->anulada()->create();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.estado.cambiar');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('No se puede cambiar');

        Livewire::actingAs($usuario)
            ->test(FacturaShow::class, ['factura' => $factura])
            ->call('cambiarEstado', 'Pagada');

        $this->assertDatabaseHas('facturas', [
            'id' => $factura->id,
            'estado' => 'Anulada',
        ]);
    }

    // =====================================================================
    // Descarga del PDF
    // =====================================================================

    /**
     * El botón "Descargar PDF" genera un PDF válido (content-type
     * application/pdf y contenido binario de PDF).
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
    protected function crearFacturaReal(int $descuentoPorcentaje = 0): Factura
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);

        $servicio = app(FacturaService::class);

        return $servicio->crear([
            'cliente_id' => Cliente::factory()->create()->id,
            'vendedor_id' => Vendedor::factory()->create()->id,
            'fecha_emision' => '2026-08-19',
            'fecha_vencimiento' => null,
            'descuento_porcentaje' => $descuentoPorcentaje,
            'notas' => null,
            'items' => [
                [
                    'producto_id' => null,
                    'descripcion' => 'Producto A',
                    'cantidad' => 2,
                    'precio_unitario' => 100,
                    'descuento_porcentaje' => 0,
                    'aplica_impuesto' => true,
                ],
            ],
        ]);
    }
}
