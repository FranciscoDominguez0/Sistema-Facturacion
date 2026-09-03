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
}
