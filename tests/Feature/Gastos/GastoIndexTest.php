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
}
