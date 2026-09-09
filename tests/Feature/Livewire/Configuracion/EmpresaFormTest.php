<?php

namespace Tests\Feature\Livewire\Configuracion;

use App\Livewire\Configuracion\EmpresaForm;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_carga_los_datos_actuales_de_la_empresa_al_montar()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Empresa::actual()->update([
            'nombre' => 'Empresa Cargada',
            'moneda' => 'EUR - Euro',
            'simbolo_moneda' => '€',
            'impuesto_nombre' => 'IVA',
            'impuesto_porcentaje' => 21,
        ]);

        Livewire::actingAs($user)
            ->test(EmpresaForm::class)
            ->assertSet('form.nombre', 'Empresa Cargada')
            ->assertSet('form.moneda', 'EUR - Euro')
            ->assertSet('form.simbolo_moneda', '€')
            ->assertSet('form.impuesto_nombre', 'IVA')
            ->assertSet('form.impuesto_porcentaje', 21);
    }

    public function test_guarda_datos_de_empresa_correctamente()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(EmpresaForm::class)
            ->set('form.nombre', 'Empresa Test SA')
            ->set('form.identificacion_fiscal', '123456789')
            ->set('form.moneda', 'EUR - Euro')
            ->set('form.simbolo_moneda', '€')
            ->set('form.impuesto_nombre', 'IVA')
            ->set('form.impuesto_porcentaje', 21)
            ->call('guardar')
            ->assertDispatched('toast');

        $empresa = Empresa::actual();
        $this->assertEquals('Empresa Test SA', $empresa->nombre);
        $this->assertEquals('123456789', $empresa->identificacion_fiscal);
        $this->assertEquals('EUR - Euro', $empresa->moneda);
        $this->assertEquals(21, $empresa->impuesto_porcentaje);
    }

    public function test_guarda_el_logo_y_reemplaza_el_anterior()
    {
        Storage::fake('public');
        Storage::disk('public')->put('logos/logo-anterior.png', 'contenido anterior');

        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Empresa::actual()->update(['logo_path' => 'logos/logo-anterior.png']);

        $logoNuevo = UploadedFile::fake()->image('logo-nuevo.png');

        Livewire::actingAs($user)
            ->test(EmpresaForm::class)
            ->set('logo', $logoNuevo)
            ->call('guardar')
            ->assertDispatched('toast')
            ->assertSet('logo', null);

        $rutaGuardada = Empresa::actual()->logo_path;

        $this->assertNotNull($rutaGuardada);
        $this->assertStringStartsWith('logos/', $rutaGuardada);
        Storage::disk('public')->assertExists($rutaGuardada);
        Storage::disk('public')->assertMissing('logos/logo-anterior.png');
    }

    public function test_valida_los_campos_obligatorios()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(EmpresaForm::class)
            ->set('form.nombre', '')
            ->set('form.moneda', '')
            ->set('form.simbolo_moneda', '')
            ->set('form.impuesto_nombre', '')
            ->call('guardar')
            ->assertHasErrors([
                'form.nombre' => 'required',
                'form.moneda' => 'required',
                'form.simbolo_moneda' => 'required',
                'form.impuesto_nombre' => 'required',
            ]);
    }

    public function test_valida_el_color_en_formato_hexadecimal()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(EmpresaForm::class)
            ->set('form.color_primario', 'rojo')
            ->call('guardar')
            ->assertHasErrors('form.color_primario');
    }
}
