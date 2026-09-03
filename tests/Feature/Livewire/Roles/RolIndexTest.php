<?php

namespace Tests\Feature\Livewire\Roles;

use App\Livewire\Roles\RolIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'empresa.gestionar']);
    }

    public function test_bloquea_acceso_sin_permisos()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(RolIndex::class)
            ->assertForbidden();
    }

    public function test_renderiza_correctamente_con_permisos()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');
        
        Role::firstOrCreate(['name' => 'Administrador']);

        Livewire::actingAs($user)
            ->test(RolIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.roles.rol-index');
    }

    public function test_crea_un_rol_y_asigna_permisos()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');
        
        $permisoFacturas = Permission::firstOrCreate(['name' => 'facturas.ver']);

        Livewire::actingAs($user)
            ->test(RolIndex::class)
            ->set('nuevoRolNombre', 'Vendedor')
            ->call('guardarRol')
            ->assertDispatched('toast')
            ->assertSet('modalRolVisible', false);

        $this->assertDatabaseHas('roles', ['name' => 'Vendedor']);

        // Ahora asignamos permisos al rol activo
        $rolVendedor = Role::where('name', 'Vendedor')->first();
        
        Livewire::actingAs($user)
            ->test(RolIndex::class)
            ->call('seleccionarRol', $rolVendedor->id)
            ->set('permisosAsignados', ['facturas.ver'])
            ->call('guardarPermisos')
            ->assertDispatched('toast');

        $this->assertTrue($rolVendedor->hasPermissionTo('facturas.ver'));
    }
}
