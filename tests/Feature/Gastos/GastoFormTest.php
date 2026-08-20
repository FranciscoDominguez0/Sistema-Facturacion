<?php

namespace Tests\Feature\Gastos;

use App\Livewire\Gastos\GastoForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GastoFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('gastos.ver');
        Permission::findOrCreate('gastos.gestionar');
    }

    public function test_usuario_con_permiso_puede_ver_el_formulario(): void
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('gastos.gestionar');

        $this->actingAs($usuario)
            ->get(route('gastos.crear'))
            ->assertOk();
    }

    public function test_usuario_sin_permiso_no_puede_acceder_al_formulario(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->get(route('gastos.crear'))
            ->assertForbidden();
    }

    public function test_guardar_gasto_valido_crea_y_redirige(): void
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('gastos.gestionar');

        Livewire::actingAs($usuario)
            ->test(GastoForm::class)
            ->set('form.concepto', 'Luz eléctrica')
            ->set('form.categoria', config('gastos.categorias')[0])
            ->set('form.monto', 150.75)
            ->set('form.fecha', '2026-08-20')
            ->set('form.comprobante', 'FAC-001')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertRedirect(route('gastos'))
            ->assertSessionHas('success', 'Gasto registrado con éxito.');

        $this->assertDatabaseHas('gastos', [
            'concepto' => 'Luz eléctrica',
            'categoria' => config('gastos.categorias')[0],
            'registrado_por' => $usuario->id,
            'comprobante' => 'FAC-001',
        ]);
    }

    public function test_guardar_sin_concepto_falla_la_validacion(): void
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('gastos.gestionar');

        Livewire::actingAs($usuario)
            ->test(GastoForm::class)
            ->set('form.categoria', config('gastos.categorias')[0])
            ->set('form.monto', 100)
            ->set('form.fecha', '2026-08-20')
            ->call('guardar')
            ->assertHasErrors(['form.concepto' => 'required']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_guardar_con_monto_invalido_falla_la_validacion(): void
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('gastos.gestionar');

        Livewire::actingAs($usuario)
            ->test(GastoForm::class)
            ->set('form.concepto', 'Agua potable')
            ->set('form.categoria', config('gastos.categorias')[0])
            ->set('form.monto', 0)
            ->set('form.fecha', '2026-08-20')
            ->call('guardar')
            ->assertHasErrors(['form.monto' => 'min']);

        $this->assertDatabaseCount('gastos', 0);
    }
}
