<?php

namespace Tests\Feature\Gastos;

use App\Livewire\Gastos\GastoIndex;
use App\Models\Gasto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GastoIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('gastos.ver');
        Permission::findOrCreate('gastos.eliminar');
    }

    public function test_muestra_los_gastos_recientes_ordenados(): void
    {
        $usuario = User::factory()->create();

        Gasto::factory()->create(['concepto' => 'Antiguo', 'fecha' => '2026-01-01']);
        Gasto::factory()->create(['concepto' => 'Reciente', 'fecha' => '2026-08-20']);

        Livewire::actingAs($usuario)
            ->test(GastoIndex::class)
            ->assertSeeInOrder(['Reciente', 'Antiguo']);
    }

    public function test_la_busqueda_filtra_por_concepto(): void
    {
        $usuario = User::factory()->create();

        Gasto::factory()->create(['concepto' => 'Luz eléctrica']);
        Gasto::factory()->create(['concepto' => 'Agua potable']);

        Livewire::actingAs($usuario)
            ->test(GastoIndex::class)
            ->set('search', 'Luz')
            ->assertSee('Luz eléctrica')
            ->assertDontSee('Agua potable');
    }

    public function test_la_tabla_muestra_el_numero_de_gasto_con_estado_registrado(): void
    {
        $usuario = User::factory()->create();
        $gasto = Gasto::factory()->create();

        Livewire::actingAs($usuario)
            ->test(GastoIndex::class)
            ->assertSee('GASTO #'.str_pad((string) $gasto->id, 6, '0', STR_PAD_LEFT))
            ->assertSee('Registrado');
    }

    public function test_eliminar_desde_el_indice_borra_el_gasto(): void
    {
        $gasto = Gasto::factory()->create();
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('gastos.eliminar');

        Livewire::actingAs($usuario)
            ->test(GastoIndex::class)
            ->call('confirmarEliminacion', $gasto->id)
            ->call('eliminar')
            ->assertDispatched('toast')
            ->assertSet('modalEliminarVisible', false)
            ->assertSet('gastoAEliminar', null);

        $this->assertDatabaseMissing('gastos', ['id' => $gasto->id]);
    }

    public function test_eliminar_sin_permiso_es_rechazado(): void
    {
        $gasto = Gasto::factory()->create();
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(GastoIndex::class)
            ->call('confirmarEliminacion', $gasto->id)
            ->call('eliminar')
            ->assertForbidden();

        $this->assertDatabaseHas('gastos', ['id' => $gasto->id]);
    }
}
