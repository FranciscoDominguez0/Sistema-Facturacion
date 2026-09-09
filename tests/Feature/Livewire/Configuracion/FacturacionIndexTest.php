<?php

namespace Tests\Feature\Livewire\Configuracion;

use App\Livewire\Configuracion\FacturacionIndex;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class FacturacionIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'empresa.gestionar']);
        Empresa::actual();
    }

    public function test_bloquea_acceso_sin_permisos()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(FacturacionIndex::class)
            ->assertForbidden();
    }

    public function test_renderiza_correctamente_con_permisos()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(FacturacionIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.configuracion.facturacion-index');
    }

    public function test_carga_la_numeracion_actual_al_montar()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Empresa::actual()->update([
            'prefijo_factura' => 'INV-',
            'siguiente_numero_factura' => 15,
        ]);

        Livewire::actingAs($user)
            ->test(FacturacionIndex::class)
            ->assertSet('form.prefijo_factura', 'INV-')
            ->assertSet('form.siguiente_numero_factura', 15);
    }

    public function test_guarda_la_numeracion_de_facturas()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(FacturacionIndex::class)
            ->set('form.prefijo_factura', 'FAC-')
            ->set('form.siguiente_numero_factura', 12)
            ->call('guardar')
            ->assertDispatched('toast');

        $empresa = Empresa::actual();
        $this->assertEquals('FAC-', $empresa->prefijo_factura);
        $this->assertEquals(12, $empresa->siguiente_numero_factura);
    }

    public function test_rechaza_prefijo_con_mas_de_diez_caracteres()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(FacturacionIndex::class)
            ->set('form.prefijo_factura', 'PREFIJO-MUY-LARGO-')
            ->call('guardar')
            ->assertHasErrors('form.prefijo_factura');

        $this->assertEquals('FAC-', Empresa::actual()->prefijo_factura);
    }

    public function test_rechaza_numero_siguiente_menor_a_uno()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(FacturacionIndex::class)
            ->set('form.siguiente_numero_factura', 0)
            ->call('guardar')
            ->assertHasErrors('form.siguiente_numero_factura');

        $this->assertEquals(1, Empresa::actual()->siguiente_numero_factura);
    }

    public function test_muestra_la_vista_previa_con_ceros_a_la_izquierda()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('empresa.gestionar');

        Livewire::actingAs($user)
            ->test(FacturacionIndex::class)
            ->set('form.prefijo_factura', 'FAC-')
            ->set('form.siguiente_numero_factura', 7)
            ->assertSee('FAC-000006')
            ->assertSee('FAC-000007')
            ->assertSee('FAC-000008');
    }
}
