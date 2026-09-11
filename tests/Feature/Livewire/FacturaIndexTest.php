<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Facturas\FacturaIndex;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\User;
use App\Services\FacturaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Tests del listado de facturas (FacturaIndex).
 *
 * El proyecto usa PHPUnit clásico con nombres de test en español.
 */
class FacturaIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('facturas.ver');
        Permission::findOrCreate('facturas.eliminar');
    }

    /**
     * Confirmar eliminación abre el modal con la factura seleccionada.
     */
    public function test_confirmar_eliminacion_carga_la_factura_en_el_modal(): void
    {
        $factura = $this->crearFacturaReal();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.eliminar');

        Livewire::actingAs($usuario)
            ->test(FacturaIndex::class)
            ->call('confirmarEliminacion', $factura->id)
            ->assertSet('modalEliminarVisible', true)
            ->assertSet('facturaAEliminar.id', $factura->id);
    }

    /**
     * Eliminar desde el índice borra la factura y sus líneas, y avisa con toast.
     */
    public function test_eliminar_desde_el_indice_borra_la_factura(): void
    {
        $factura = $this->crearFacturaReal();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.eliminar');

        Livewire::actingAs($usuario)
            ->test(FacturaIndex::class)
            ->call('confirmarEliminacion', $factura->id)
            ->call('eliminar')
            ->assertDispatched('toast')
            ->assertSet('modalEliminarVisible', false)
            ->assertSet('facturaAEliminar', null);

        $this->assertDatabaseMissing('facturas', ['id' => $factura->id]);
        $this->assertDatabaseMissing('factura_items', ['factura_id' => $factura->id]);
    }

    /**
     * Sin el permiso de eliminar, la acción es rechazada y la factura sigue.
     */
    public function test_eliminar_sin_permiso_es_rechazado(): void
    {
        $factura = $this->crearFacturaReal();
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FacturaIndex::class)
            ->call('confirmarEliminacion', $factura->id)
            ->call('eliminar')
            ->assertForbidden();

        $this->assertDatabaseHas('facturas', ['id' => $factura->id]);
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

        return $servicio->crear([
            'cliente_id' => Cliente::factory()->create()->id,
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
