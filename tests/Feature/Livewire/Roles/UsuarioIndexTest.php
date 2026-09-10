<?php

namespace Tests\Feature\Livewire\Roles;

use App\Livewire\Roles\UsuarioIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UsuarioIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'usuarios.ver']);
        Permission::firstOrCreate(['name' => 'usuarios.crear']);
        Permission::firstOrCreate(['name' => 'usuarios.editar']);
        Permission::firstOrCreate(['name' => 'usuarios.eliminar']);
    }

    public function test_bloquea_acceso_sin_permisos()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->assertForbidden();
    }

    public function test_renderiza_correctamente_con_permisos()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.roles.usuario-index');
    }

    public function test_elimina_usuario_con_permiso()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');
        $user->givePermissionTo('usuarios.eliminar');

        $userAEliminar = User::factory()->create();

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->call('confirmarEliminacion', $userAEliminar->id)
            ->call('eliminarUsuario')
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('users', ['id' => $userAEliminar->id]);
    }

    public function test_elimina_usuarios_seleccionados()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');
        $user->givePermissionTo('usuarios.eliminar');

        $primerUsuario = User::factory()->create();
        $segundoUsuario = User::factory()->create();

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->set('seleccionados', [$primerUsuario->id, $segundoUsuario->id])
            ->call('eliminarSeleccionados')
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('users', ['id' => $primerUsuario->id]);
        $this->assertDatabaseMissing('users', ['id' => $segundoUsuario->id]);
    }

    public function test_no_elimina_al_usuario_autenticado_en_seleccion_masiva()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');
        $user->givePermissionTo('usuarios.eliminar');

        $otroUsuario = User::factory()->create();

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->set('seleccionados', [$user->id, $otroUsuario->id])
            ->call('eliminarSeleccionados');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('users', ['id' => $otroUsuario->id]);
    }

    public function test_filtra_usuarios_por_estado()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');

        $activo = User::factory()->create(['activo' => true]);
        $inactivo = User::factory()->create(['activo' => false]);

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->set('filtroEstado', 'Activo')
            ->assertSee($activo->name)
            ->assertDontSee($inactivo->name);
    }

    public function test_crea_usuario_solo_con_nombre()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');
        $user->givePermissionTo('usuarios.crear');

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->call('crearUsuario')
            ->set('form.name', 'Solo Nombre')
            ->call('guardarUsuario')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('users', ['name' => 'Solo Nombre', 'email' => null]);
    }

    public function test_confirmar_eliminacion_masiva_abre_modal()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->call('confirmarEliminacionMasiva')
            ->assertSet('modalEliminarMasivoVisible', true);
    }

    public function test_guardar_usuario_editado_muestra_mensaje_de_actualizacion()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');
        $user->givePermissionTo('usuarios.editar');

        $aEditar = User::factory()->create(['name' => 'Nombre Original']);

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->call('editarUsuario', $aEditar->id)
            ->set('form.name', 'Nombre Actualizado')
            ->call('guardarUsuario')
            ->assertDispatched('toast', message: 'Usuario actualizado exitosamente.');

        $this->assertDatabaseHas('users', ['id' => $aEditar->id, 'name' => 'Nombre Actualizado']);
    }

    public function test_muestra_la_paginacion_con_mas_de_cinco_usuarios()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');

        User::factory()->count(6)->create();

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->assertSee('chevron_right')
            ->assertSee('aria-label="Página siguiente"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_la_paginacion_de_settings_usuarios_no_duplica_el_prefijo()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');

        User::factory()->count(6)->create();

        $respuesta = $this->actingAs($user)->get('/settings/usuarios');

        $respuesta->assertOk();

        preg_match_all('/href="([^"]*page=2[^"]*)"[^>]*wire:navigate/', $respuesta->getContent(), $coincidencias);

        $hrefs = $coincidencias[1] ?? [];

        $this->assertNotEmpty($hrefs, 'Debe existir un enlace a la página 2.');
        foreach ($hrefs as $href) {
            $this->assertStringNotContainsString('/settings/settings/', $href);
            $this->assertStringStartsWith('/settings/usuarios?page=2', $href);
        }
    }

    public function test_permisos_distintos_no_comparten_estado()
    {
        Permission::firstOrCreate(['name' => 'clientes.editar']);

        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');

        $aEditar = User::factory()->create();
        $aEditar->givePermissionTo('clientes.editar');

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->call('editarUsuario', $aEditar->id)
            ->assertSet('form.permisos', ['clientes.editar']);
    }

    public function test_seleccionar_todos_marca_los_usuarios_de_la_pagina()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('usuarios.ver');

        $otrosUsuarios = User::factory()->count(5)->create();
        $idsEsperados = collect([$user, ...$otrosUsuarios])->pluck('id')->sort()->values()->all();

        Livewire::actingAs($user)
            ->test(UsuarioIndex::class)
            ->call('seleccionarTodos')
            ->assertSet('seleccionados', $idsEsperados);
    }
}
