<?php

namespace Tests\Feature\Livewire;

use App\Enums\EstadoFactura;
use App\Livewire\Dashboard;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_renderiza_el_dashboard()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.dashboard');
    }

    public function test_el_grafico_por_defecto_muestra_seis_meses()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSet('chartData.meses', fn ($meses) => count($meses) === 6)
            ->assertSet('chartData.ingresos', fn ($ingresos) => count($ingresos) === 6)
            ->assertSet('chartData.volumen', fn ($volumen) => count($volumen) === 6);
    }

    public function test_el_grafico_de_doce_meses_muestra_doce_meses()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('cambiarPeriodoGrafico', '12M')
            ->assertSet('chartData.meses', fn ($meses) => count($meses) === 12);
    }

    public function test_el_grafico_de_anio_actual_muestra_solo_los_meses_transcurridos()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('cambiarPeriodoGrafico', 'anio')
            ->assertSet('chartData.meses', fn ($meses) => count($meses) === Carbon::now()->month);
    }

    public function test_el_periodo_de_siete_dias_solo_suma_las_facturas_de_ese_rango()
    {
        $user = User::factory()->create();

        Factura::factory()->create(['fecha_emision' => Carbon::now()->subDays(3), 'total' => 500, 'estado' => EstadoFactura::PAGADA]);
        Factura::factory()->create(['fecha_emision' => Carbon::now()->subDays(10), 'total' => 9999, 'estado' => EstadoFactura::PAGADA]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('cambiarPeriodo', '7d')
            ->assertSet('ventasTotales', 500.0)
            ->assertSet('totalFacturas', 1);
    }

    public function test_el_filtro_de_estado_filtra_las_facturas_recientes()
    {
        $user = User::factory()->create();

        Factura::factory()->pagada()->create();
        Factura::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('filtroEstado', EstadoFactura::PAGADA->value)
            ->assertSet('facturasRecientes', fn ($facturas) => collect($facturas)->every(
                fn ($factura) => $factura->estado === EstadoFactura::PAGADA
            ));
    }

    public function test_los_indicadores_usan_el_periodo_del_mes_por_defecto()
    {
        $user = User::factory()->create();

        Cliente::factory()->create(['created_at' => Carbon::now()->subMonths(2)]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSet('nuevosClientes', 0);
    }

    public function test_el_kpi_de_gastos_solo_suma_los_gastos_del_periodo()
    {
        $user = User::factory()->create();

        Gasto::factory()->create(['fecha' => Carbon::now()->subDays(3), 'monto' => 300]);
        Gasto::factory()->create(['fecha' => Carbon::now()->subDays(10), 'monto' => 9999]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('cambiarPeriodo', '7d')
            ->assertSet('gastosTotales', 300.0);
    }

    public function test_el_kpi_de_gastos_crece_frente_al_periodo_anterior()
    {
        $user = User::factory()->create();

        Gasto::factory()->create(['fecha' => Carbon::now()->subDays(9), 'monto' => 100]);
        Gasto::factory()->create(['fecha' => Carbon::now()->subDays(3), 'monto' => 200]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('cambiarPeriodo', '7d')
            ->assertSet('gastosCrecimiento', 100.0);
    }

    public function test_los_productos_mas_vendidos_incluyen_la_imagen_del_producto()
    {
        $user = User::factory()->create();

        $producto = Producto::factory()->create(['imagen_path' => 'productos/foto.png']);

        $factura = Factura::factory()->pagada()->create(['fecha_emision' => Carbon::now()]);

        FacturaItem::create([
            'factura_id' => $factura->id,
            'producto_id' => $producto->id,
            'descripcion' => $producto->descripcion,
            'cantidad' => 2,
            'precio_unitario' => 25,
            'subtotal_linea' => 50,
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSet('productosMasVendidos', fn ($productos) => $productos->first()->imagen_path === 'productos/foto.png');
    }
}
