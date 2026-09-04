<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\User;

use App\Services\FacturaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests de rutas y permisos del módulo de facturas.
 *
 * El proyecto usa PHPUnit clásico con nombres de test en español.
 */
class FacturaRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('facturas.ver');
        Permission::findOrCreate('facturas.gestionar');
        Role::findOrCreate('Cajero');
    }

    /**
     * Todas las rutas de facturas requieren autenticación: un invitado es
     * redirigido al login.
     */
    public function test_un_invitado_es_redirigido_al_login_en_las_rutas_de_facturas(): void
    {
        $factura = Factura::factory()->create();

        $this->get(route('facturas'))->assertRedirect(route('login'));
        $this->get(route('facturas.crear'))->assertRedirect(route('login'));
        $this->get(route('facturas.show', $factura))->assertRedirect(route('login'));
        $this->get(route('facturas.pdf', $factura))->assertRedirect(route('login'));
    }

    /**
     * Las rutas usan permisos vía can(), no comparación directa de nombres
     * de rol: un usuario con un rol distinto al de administrador pero con el
     * permiso explícito asignado sí puede acceder.
     */
    public function test_un_usuario_con_rol_distinto_pero_con_permiso_explicito_puede_acceder(): void
    {
        $factura = $this->crearFacturaReal();

        $usuario = User::factory()->create();
        $usuario->assignRole('Cajero');
        $usuario->givePermissionTo('facturas.ver', 'facturas.gestionar');

        $this->actingAs($usuario)->get(route('facturas.crear'))->assertOk();
        $this->actingAs($usuario)->get(route('facturas.show', $factura))->assertOk();
        $this->actingAs($usuario)->get(route('facturas.pdf', $factura))->assertOk();
    }

    /**
     * Un usuario autenticado sin los permisos recibe 403 en las rutas que los
     * exigen (crear, ver y PDF), aunque esté autenticado.
     */
    public function test_un_usuario_autenticado_sin_permisos_recibe_403(): void
    {
        $factura = $this->crearFacturaReal();
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->get(route('facturas.crear'))->assertForbidden();
        $this->actingAs($usuario)->get(route('facturas.show', $factura))->assertForbidden();
        $this->actingAs($usuario)->get(route('facturas.pdf', $factura))->assertForbidden();
    }

    /**
     * El listado de facturas está disponible para cualquier usuario
     * autenticado (no exige permiso de ver).
     */
    public function test_el_listado_esta_disponible_para_cualquier_usuario_autenticado(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->get(route('facturas'))->assertOk();
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
                    'aplica_impuesto' => true,
                ],
            ],
        ]);
    }
}
