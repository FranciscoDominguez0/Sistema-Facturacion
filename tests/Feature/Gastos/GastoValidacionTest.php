<?php

namespace Tests\Feature\Gastos;

use App\Livewire\Gastos\GastoForm;
use App\Models\Gasto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GastoValidacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('gastos.ver');
        Permission::findOrCreate('gastos.gestionar');
    }

    private function datosValidos(): array
    {
        return [
            'form.concepto' => 'Luz eléctrica',
            'form.categoria' => config('gastos.categorias')[0],
            'form.monto' => '150.75',
            'form.fecha' => '2026-08-20',
            'form.comprobante' => 'FAC-001',
        ];
    }

    private function usuarioConPermiso(): User
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('gastos.gestionar');

        return $usuario;
    }

    public function test_monto_negativo_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', '-50')
            ->call('guardar')
            ->assertHasErrors(['form.monto' => 'min']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_monto_cero_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', '0')
            ->call('guardar')
            ->assertHasErrors(['form.monto' => 'min']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_monto_menor_al_minimo_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', '0.001')
            ->call('guardar')
            ->assertHasErrors(['form.monto' => 'min']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_monto_no_numerico_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', 'abc')
            ->call('guardar')
            ->assertHasErrors(['form.monto' => 'numeric']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_monto_con_texto_embebido_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', '12.5abc')
            ->call('guardar')
            ->assertHasErrors(['form.monto' => 'numeric']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_monto_con_separador_de_miles_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', '1,000.00')
            ->call('guardar')
            ->assertHasErrors(['form.monto' => 'numeric']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_monto_vacio_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', '')
            ->call('guardar')
            ->assertHasErrors(['form.monto' => 'required']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_concepto_vacio_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.concepto', '')
            ->call('guardar')
            ->assertHasErrors(['form.concepto' => 'required']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_concepto_demasiado_largo_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.concepto', str_repeat('A', 256))
            ->call('guardar')
            ->assertHasErrors(['form.concepto' => 'max']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_categoria_vacia_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.categoria', '')
            ->call('guardar')
            ->assertHasErrors('form.categoria');

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_categoria_no_valida_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.categoria', 'Categoría inventada')
            ->call('guardar')
            ->assertHasErrors(['form.categoria' => 'in']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_categoria_con_distincion_de_mayusculas_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.categoria', mb_strtolower(config('gastos.categorias')[0]))
            ->call('guardar')
            ->assertHasErrors(['form.categoria' => 'in']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_fecha_vacia_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.fecha', '')
            ->call('guardar')
            ->assertHasErrors(['form.fecha' => 'required']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_fecha_no_valida_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.fecha', 'no-es-una-fecha')
            ->call('guardar')
            ->assertHasErrors(['form.fecha' => 'date']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_fecha_con_dia_inexistente_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.fecha', '2026-02-30')
            ->call('guardar')
            ->assertHasErrors(['form.fecha' => 'date']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_fecha_con_mes_invalido_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.fecha', '2026-13-01')
            ->call('guardar')
            ->assertHasErrors(['form.fecha' => 'date']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_comprobante_demasiado_largo_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.comprobante', str_repeat('X', 256))
            ->call('guardar')
            ->assertHasErrors(['form.comprobante' => 'max']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_monto_minimo_aceptado_crea_el_gasto(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', '0.01')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertRedirect(route('gastos'));

        $this->assertDatabaseCount('gastos', 1);
    }

    public function test_monto_con_decimales_aceptado_crea_el_gasto(): void
    {
        $usuario = $this->usuarioConPermiso();

        Livewire::actingAs($usuario)
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', '1234.56')
            ->call('guardar')
            ->assertHasNoErrors();

        $gasto = Gasto::where('registrado_por', $usuario->id)->first();
        $this->assertSame('1234.56', $gasto->monto);
    }

    public function test_monto_grande_dentro_del_limite_decimal_crea_el_gasto(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', '9999999999.99')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('gastos', 1);
    }

    public function test_monto_que_excede_el_maximo_falla_la_validacion(): void
    {
        Livewire::actingAs($this->usuarioConPermiso())
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.monto', '10000000000.00')
            ->call('guardar')
            ->assertHasErrors(['form.monto' => 'max']);

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_comprobante_vacio_se_guarda_como_nulo(): void
    {
        $usuario = $this->usuarioConPermiso();

        Livewire::actingAs($usuario)
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.comprobante', '')
            ->call('guardar')
            ->assertHasNoErrors();

        $gasto = Gasto::where('registrado_por', $usuario->id)->first();
        $this->assertNull($gasto->comprobante);
    }

    public function test_caracteres_especiales_en_concepto_se_guardan_correctamente(): void
    {
        $usuario = $this->usuarioConPermiso();
        $concepto = '<script>alert(1)</script> & "comillas" \'simples\'';

        Livewire::actingAs($usuario)
            ->test(GastoForm::class)
            ->set($this->datosValidos())
            ->set('form.concepto', $concepto)
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('gastos', [
            'concepto' => $concepto,
            'registrado_por' => $usuario->id,
        ]);
    }
}
