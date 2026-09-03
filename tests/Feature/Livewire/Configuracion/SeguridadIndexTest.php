<?php

namespace Tests\Feature\Livewire\Configuracion;

use App\Livewire\Configuracion\SeguridadIndex;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SeguridadIndexTest extends TestCase
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
            ->test(SeguridadIndex::class)
            ->assertForbidden();
    }

    public function test_renderiza_correctamente_con_permisos()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(SeguridadIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.configuracion.seguridad-index');
    }

    public function test_guarda_configuracion_correctamente()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(SeguridadIndex::class)
            ->set('password_length', 14)
            ->set('session_timeout', 60)
            ->set('login_lockout', false)
            ->call('guardarSeguridad')
            ->assertDispatched('toast');

        $empresa = Empresa::actual();
        $this->assertEquals(14, $empresa->password_length);
        $this->assertEquals(60, $empresa->session_timeout);
        $this->assertFalse((bool)$empresa->login_lockout);
    }
}
