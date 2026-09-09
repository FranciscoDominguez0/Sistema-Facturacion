<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Tests de visibilidad del sidebar según los permisos del usuario.
 *
 * Un módulo solo se muestra si el usuario tiene al menos uno de sus permisos.
 * El proyecto usa PHPUnit clásico con nombres de test en español.
 */
class SidebarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar',
            'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar',
            'facturas.ver', 'facturas.crear', 'facturas.editar', 'facturas.eliminar',
            'gastos.ver', 'gastos.crear', 'gastos.editar', 'gastos.eliminar',
            'empresa.gestionar',
            'usuarios.ver',
        ] as $permiso) {
            Permission::findOrCreate($permiso);
        }
    }

    /**
     * Un usuario sin ningún permiso solo ve el enlace de Inicio.
     */
    public function test_un_usuario_sin_permisos_solo_ve_inicio_en_el_sidebar(): void
    {
        $usuario = User::factory()->create();

        $respuesta = $this->verSidebar($usuario);

        $respuesta->assertOk();
        $respuesta->assertSee('href="'.route('dashboard').'"', false);
        $respuesta->assertDontSee('href="'.route('clientes').'"', false);
        $respuesta->assertDontSee('href="'.route('productos.index').'"', false);
        $respuesta->assertDontSee('href="'.route('facturas').'"', false);
        $respuesta->assertDontSee('href="'.route('gastos').'"', false);
        $respuesta->assertDontSee('href="'.route('settings.empresa').'"', false);
    }

    /**
     * Basta un solo permiso de un módulo (aunque no sea el de ver) para que
     * el enlace del módulo aparezca en el sidebar.
     */
    public function test_un_usuario_con_un_solo_permiso_del_modulo_ve_el_enlace_del_modulo(): void
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('clientes.crear');

        $respuesta = $this->verSidebar($usuario);

        $respuesta->assertSee('href="'.route('clientes').'"', false);
        $respuesta->assertDontSee('href="'.route('productos.index').'"', false);
        $respuesta->assertDontSee('href="'.route('facturas').'"', false);
        $respuesta->assertDontSee('href="'.route('gastos').'"', false);
        $respuesta->assertDontSee('href="'.route('settings.empresa').'"', false);
    }

    /**
     * Cualquiera de los permisos de configuración (empresa o usuarios) hace
     * visible el enlace de Configuración.
     */
    public function test_un_usuario_con_un_permiso_de_configuracion_ve_el_enlace(): void
    {
        foreach (['empresa.gestionar', 'usuarios.ver'] as $permiso) {
            $usuario = User::factory()->create();
            $usuario->givePermissionTo($permiso);

            $this->verSidebar($usuario)->assertSee('href="'.route('settings.empresa').'"', false);
        }
    }

    /**
     * Un usuario con permisos de varios módulos ve todos sus enlaces.
     */
    public function test_un_usuario_con_permisos_de_varios_modulos_ve_todos_sus_enlaces(): void
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo([
            'clientes.ver', 'productos.ver', 'facturas.ver', 'gastos.ver', 'empresa.gestionar',
        ]);

        $respuesta = $this->verSidebar($usuario);

        $respuesta->assertSee('href="'.route('clientes').'"', false);
        $respuesta->assertSee('href="'.route('productos.index').'"', false);
        $respuesta->assertSee('href="'.route('facturas').'"', false);
        $respuesta->assertSee('href="'.route('gastos').'"', false);
        $respuesta->assertSee('href="'.route('settings.empresa').'"', false);
    }

    /**
     * Renderiza el dashboard (usa el layout con el sidebar) como el usuario.
     */
    protected function verSidebar(User $usuario): TestResponse
    {
        return $this->actingAs($usuario)->get(route('dashboard'));
    }
}
