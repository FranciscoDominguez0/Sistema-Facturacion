<?php

namespace Tests\Feature\Livewire\Configuracion;

use App\Livewire\Configuracion\EmpresaForm;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EmpresaFormTest extends TestCase
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
            ->test(EmpresaForm::class)
            ->assertForbidden();
    }

    public function test_renderiza_correctamente_con_permisos()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(EmpresaForm::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.configuracion.empresa-form');
    }

    public function test_guarda_datos_de_empresa_correctamente()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(EmpresaForm::class)
            ->set('nombre', 'Empresa Test SA')
            ->set('identificacion_fiscal', '123456789')
            ->set('moneda', 'EUR - Euro')
            ->set('simbolo_moneda', '€')
            ->set('impuesto_nombre', 'IVA')
            ->set('impuesto_porcentaje', 21)
            ->call('guardar')
            ->assertDispatched('toast');

        $empresa = Empresa::actual();
        $this->assertEquals('Empresa Test SA', $empresa->nombre);
        $this->assertEquals('123456789', $empresa->identificacion_fiscal);
        $this->assertEquals('EUR - Euro', $empresa->moneda);
        $this->assertEquals(21, $empresa->impuesto_porcentaje);
    }
}
