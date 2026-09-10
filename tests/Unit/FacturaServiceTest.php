<?php

namespace Tests\Unit;

use App\Enums\EstadoFactura;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\User;
use App\Services\FacturaService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tests unitarios del servicio de facturación (FacturaService).
 *
 * El proyecto usa PHPUnit clásico, por lo que cada caso se define como
 * método test_* con nombres descriptivos en español. Aunque viven en
 * tests/Unit, estos tests tocan la base de datos (RefreshDatabase)
 * porque generarNumero(), crear() y actualizar() persisten datos.
 */
class FacturaServiceTest extends TestCase
{
    use RefreshDatabase;

    private FacturaService $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servicio = new FacturaService;
    }

    // =====================================================================
    // calcularTotales()
    // =====================================================================

    /**
     * calcularTotales() calcula subtotal, descuento_total, impuesto y total
     * con varias líneas, cada una con su propio impuesto.
     */
    public function test_calcular_totales_con_varias_lineas(): void
    {
        $items = [
            ['cantidad' => 2, 'precio_unitario' => 100, 'descuento_porcentaje' => 0, 'impuesto_porcentaje' => 7, 'impuesto_nombre' => 'ITBMS'],
            ['cantidad' => 1, 'precio_unitario' => 50, 'descuento_porcentaje' => 10, 'impuesto_porcentaje' => 7, 'impuesto_nombre' => 'ITBMS'],
        ];

        $resultado = $this->servicio->calcularTotales($items);

        // Línea 1: 200. Línea 2: bruto 50, desc. 5, subtotal 45. Subtotal = 245.
        $this->assertSame(245.0, $resultado['subtotal']);
        $this->assertSame(0.0, $resultado['descuento_total']);
        // Impuesto línea 1: 200 * 7% = 14. Línea 2: 45 * 7% = 3.15. Total impuesto = 17.15
        $this->assertSame(17.15, $resultado['impuesto']);
        $this->assertSame(262.15, $resultado['total']);

        // Los montos calculados por línea también se devuelven
        $this->assertSame(5.0, $resultado['items_actualizados'][1]['descuento_monto']);
        $this->assertSame(45.0, $resultado['items_actualizados'][1]['subtotal_linea']);
    }

    /**
     * calcularTotales() con descuento en 0 no altera el subtotal.
     */
    public function test_calcular_totales_con_descuento_cero_no_altera_el_subtotal(): void
    {
        $items = [
            ['cantidad' => 3, 'precio_unitario' => 10, 'descuento_porcentaje' => 0, 'impuesto_porcentaje' => 7],
            ['cantidad' => 2, 'precio_unitario' => 5, 'descuento_porcentaje' => 0, 'impuesto_porcentaje' => 7],
        ];

        $resultado = $this->servicio->calcularTotales($items, 0);

        $this->assertSame(40.0, $resultado['subtotal']);
        $this->assertSame(0.0, $resultado['descuento_total']);
        $this->assertSame(40.0, $resultado['subtotal']);
    }

    /**
     * calcularTotales() aplica el impuesto_porcentaje configurado por línea.
     */
    public function test_calcular_totales_aplica_el_impuesto_de_la_empresa(): void
    {
        $items = [
            ['cantidad' => 2, 'precio_unitario' => 100, 'descuento_porcentaje' => 0, 'impuesto_porcentaje' => 7],
        ];

        // Impuesto del 7% por línea
        $resultado = $this->servicio->calcularTotales($items);

        $this->assertSame(14.0, $resultado['impuesto']);
        $this->assertSame(214.0, $resultado['total']);

        // Impuesto del 15% por línea
        $items[0]['impuesto_porcentaje'] = 15;
        $resultado = $this->servicio->calcularTotales($items);

        $this->assertSame(30.0, $resultado['impuesto']);
        $this->assertSame(230.0, $resultado['total']);
    }

    /**
     * calcularTotales() redondea correctamente montos con decimales,
     * evitando errores de coma flotante.
     */
    public function test_calcular_totales_redondea_montos_con_decimales(): void
    {
        $items = [
            // 3 * 0.10 en coma flotante da 0.30000000000000004
            ['cantidad' => 3, 'precio_unitario' => 0.10, 'descuento_porcentaje' => 0, 'impuesto_porcentaje' => 0],
            ['cantidad' => 1, 'precio_unitario' => 0.70, 'descuento_porcentaje' => 0, 'impuesto_porcentaje' => 0],
        ];

        $resultado = $this->servicio->calcularTotales($items, 0);

        $this->assertSame(1.0, $resultado['subtotal']);
        $this->assertSame(0.0, $resultado['impuesto']);
        $this->assertSame(1.0, $resultado['total']);

        // Con impuesto: 33.333... * 7% redondeado a 2 decimales
        $items = [
            ['cantidad' => 10, 'precio_unitario' => 3.333, 'descuento_porcentaje' => 0, 'impuesto_porcentaje' => 7],
        ];

        $resultado = $this->servicio->calcularTotales($items, 0);

        $this->assertSame(33.33, $resultado['subtotal']);
        $this->assertSame(2.33, $resultado['impuesto']);
        $this->assertSame(35.66, $resultado['total']);
    }

    // =====================================================================
    // generarNumero()
    // =====================================================================

    /**
     * generarNumero() genera el primer número como "FAC-000001" cuando no
     * hay facturas previas (crea la empresa por defecto si no existe).
     */
    public function test_generar_numero_primero_es_fac_000001(): void
    {
        $this->assertDatabaseCount('empresas', 0);

        $numero = $this->servicio->generarNumero();

        $this->assertSame('FAC-000001', $numero);

        // La empresa por defecto queda configurada y con la secuencia avanzada
        $this->assertDatabaseHas('empresas', [
            'prefijo_factura' => 'FAC-',
            'siguiente_numero_factura' => 2,
        ]);
    }

    /**
     * generarNumero() incrementa correctamente el correlativo tras cada
     * factura creada.
     */
    public function test_generar_numero_incrementa_el_correlativo(): void
    {
        Empresa::factory()->create([
            'prefijo_factura' => 'FAC-',
            'siguiente_numero_factura' => 5,
        ]);

        $this->assertSame('FAC-000005', $this->servicio->generarNumero());
        $this->assertSame('FAC-000006', $this->servicio->generarNumero());
        $this->assertSame('FAC-000007', $this->servicio->generarNumero());

        $this->assertSame(8, Empresa::first()->siguiente_numero_factura);
    }

    /**
     * generarNumero() nunca genera números duplicados en una secuencia de
     * llamadas.
     */
    public function test_generar_numero_no_genera_duplicados_en_secuencia(): void
    {
        Empresa::factory()->create(['siguiente_numero_factura' => 1]);

        $numeros = [];

        for ($i = 0; $i < 10; $i++) {
            $numeros[] = $this->servicio->generarNumero();
        }

        $this->assertSame(10, count(array_unique($numeros)));
        $this->assertSame('FAC-000001', $numeros[0]);
        $this->assertSame('FAC-000010', $numeros[9]);
    }

    /**
     * generarNumero() es seguro ante condiciones de carrera: el lockForUpdate
     * sobre la empresa impide que dos transacciones generen el mismo número
     * en paralelo. Se verifica de forma determinista abriendo dos
     * transacciones y comprobando que la segunda queda bloqueada.
     */
    public function test_generar_numero_es_seguro_ante_condiciones_de_carrera(): void
    {
        // La empresa debe ser visible para las dos conexiones: se inserta con
        // una conexión aparte (commit real), porque los datos creados dentro
        // de la transacción de RefreshDatabase no los ve la segunda conexión.
        config(['database.connections.pgsql_race' => config('database.connections.pgsql')]);
        $conexionB = DB::connection('pgsql_race');
        $idEmpresa = $conexionB->table('empresas')->insertGetId([
            'nombre' => 'Empresa Test',
            'identificacion_fiscal' => '000000000',
            'moneda' => 'USD',
            'simbolo_moneda' => '$',
            'impuesto_nombre' => 'ITBMS',
            'impuesto_porcentaje' => 7,
            'prefijo_factura' => 'FAC-',
            'siguiente_numero_factura' => 1,
            'color_primario' => '#000000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // La fila se insertó con commit real, así que se borra al final, después
        // del rollback de RefreshDatabase (cuando el lock ya fue liberado).
        $this->beforeApplicationDestroyed(function () use ($conexionB, $idEmpresa) {
            try {
                $conexionB->table('empresas')->where('id', $idEmpresa)->delete();
            } catch (\Throwable) {
                // ya eliminada
            }

            DB::purge('pgsql_race');
        });

        // Transacción A (conexión por defecto): genera un número y mantiene
        // el lock sobre la fila de la empresa mientras no haga commit.
        DB::beginTransaction();
        $numeroA = $this->servicio->generarNumero();
        $this->assertSame('FAC-000001', $numeroA);

        // Transacción B (segunda conexión): intenta leer la misma fila con
        // FOR UPDATE. Si el lock funciona, debe bloquearse y fallar por
        // timeout, no generar un segundo número.
        $conexionB->beginTransaction();
        $conexionB->statement("SET LOCAL lock_timeout = '1500'");

        try {
            $conexionB->table('empresas')->where('id', $idEmpresa)->lockForUpdate()->first();
            $this->fail('La segunda transacción debió quedar bloqueada esperando el lock de la empresa.');
        } catch (QueryException $e) {
            $this->assertStringContainsString('lock', strtolower($e->getMessage()));
        } finally {
            $conexionB->rollBack();
        }

        // Al liberar la transacción A la secuencia continúa sin duplicados.
        DB::rollBack();

        $this->assertSame('FAC-000001', $this->servicio->generarNumero());
    }

    // =====================================================================
    // crear()
    // =====================================================================

    /**
     * crear() guarda la factura y sus items y asigna el estado inicial
     * "Pendiente".
     */
    public function test_crear_guarda_la_factura_con_sus_items_y_estado_pendiente(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7, 'siguiente_numero_factura' => 1]);
        $datos = $this->datosParaCrear();

        $factura = $this->servicio->crear($datos);

        $this->assertDatabaseHas('facturas', [
            'id' => $factura->id,
            'numero_factura' => 'FAC-000001',
            'estado' => 'Pendiente',
            'subtotal' => 200,
            'impuesto' => 14,
            'total' => 214,
        ]);

        $this->assertSame(EstadoFactura::PENDIENTE, $factura->estado);
        $this->assertDatabaseCount('factura_items', 1);
        $this->assertDatabaseHas('factura_items', [
            'factura_id' => $factura->id,
            'descripcion' => 'Producto A',
            'cantidad' => 2,
            'precio_unitario' => 100,
            'subtotal_linea' => 200,
        ]);
    }

    /**
     * crear() falla con un item inválido y la transacción revierte todo:
     * ni la factura ni los items quedan creados, y la secuencia no avanza.
     */
    public function test_crear_revierte_todo_si_falla_un_item(): void
    {
        $empresa = Empresa::factory()->create(['siguiente_numero_factura' => 1]);
        $datos = $this->datosParaCrear();

        // Se simula un fallo justo antes de insertar el item, dentro de la transacción
        FacturaItem::creating(function () {
            throw new \Exception('Error simulado al guardar el item');
        });

        try {
            $this->servicio->crear($datos);
            $this->fail('La creación debió fallar y lanzar una excepción.');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('Error simulado', $e->getMessage());
        }

        $this->assertDatabaseCount('facturas', 0);
        $this->assertDatabaseCount('factura_items', 0);
        $this->assertSame(1, $empresa->fresh()->siguiente_numero_factura);
    }

    // =====================================================================
    // actualizar()
    // =====================================================================

    /**
     * actualizar() recalcula los totales cuando cambian las líneas.
     */
    public function test_actualizar_recalcula_los_totales_con_las_nuevas_lineas(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $factura = $this->servicio->crear($this->datosParaCrear());

        $this->assertSame(200.0, (float) $factura->subtotal);
        $this->assertSame(214.0, (float) $factura->total);

        $factura = $this->servicio->actualizar($factura, [
            'descuento_porcentaje' => 0,
            'items' => [
                [
                    'producto_id' => null,
                    'descripcion' => 'Producto B',
                    'cantidad' => 3,
                    'precio_unitario' => 50,
                    'descuento_porcentaje' => 0,
                    'impuesto_porcentaje' => 7,
                    'impuesto_nombre' => 'ITBMS',
                ],
            ],
        ]);

        // 3 x 50 = 150 subtotal, 150 * 7% = 10.50 impuesto, total 160.50
        $factura->refresh();

        $this->assertSame('150.00', $factura->subtotal);
        $this->assertSame('10.50', $factura->impuesto);
        $this->assertSame('160.50', $factura->total);

        // Las líneas viejas se reemplazan por las nuevas
        $this->assertDatabaseCount('factura_items', 1);
        $this->assertDatabaseHas('factura_items', [
            'factura_id' => $factura->id,
            'descripcion' => 'Producto B',
            'cantidad' => 3,
        ]);
    }

    // =====================================================================
    // anular() y cambiarEstado()
    // =====================================================================

    /**
     * anular() cambia el estado de la factura a "Anulada".
     */
    public function test_anular_cambia_el_estado_a_anulada(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $factura = $this->servicio->crear($this->datosParaCrear());

        $factura = $this->servicio->anular($factura);

        $this->assertSame(EstadoFactura::ANULADA, $factura->estado);
        $this->assertDatabaseHas('facturas', ['id' => $factura->id, 'estado' => 'Anulada']);
    }

    /**
     * anular() no permite anular una factura ya anulada: lanza excepción.
     */
    public function test_anular_no_permite_anular_una_factura_ya_anulada(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $factura = $this->servicio->crear($this->datosParaCrear());
        $this->servicio->anular($factura);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('ya está anulada');

        $this->servicio->anular($factura);
    }

    /**
     * cambiarEstado() permite las transiciones válidas (Pendiente → Pagada,
     * Pendiente → Anulada).
     */
    public function test_cambiar_estado_permite_las_transiciones_validas(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $factura = $this->servicio->crear($this->datosParaCrear());

        $factura = $this->servicio->cambiarEstado($factura, EstadoFactura::PAGADA);

        $this->assertSame(EstadoFactura::PAGADA, $factura->estado);

        $factura = $this->servicio->cambiarEstado($factura, EstadoFactura::PENDIENTE);

        $this->assertSame(EstadoFactura::PENDIENTE, $factura->estado);
    }

    /**
     * cambiarEstado() rechaza la transición de "Anulada" a "Pagada":
     * la factura anulada es terminal.
     */
    public function test_cambiar_estado_no_permite_salir_de_anulada_a_pagada(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $factura = $this->servicio->crear($this->datosParaCrear());
        $this->servicio->anular($factura);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('No se puede cambiar');

        $this->servicio->cambiarEstado($factura, EstadoFactura::PAGADA);

        $this->assertDatabaseHas('facturas', ['id' => $factura->id, 'estado' => 'Anulada']);
    }

    /**
     * cambiarEstado() rechaza cualquier transición desde una factura anulada.
     */
    public function test_cambiar_estado_no_permite_transiciones_desde_anulada(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $factura = $this->servicio->crear($this->datosParaCrear());
        $this->servicio->anular($factura);

        try {
            $this->servicio->cambiarEstado($factura, EstadoFactura::PENDIENTE);
            $this->fail('No debió permitir salir del estado Anulada.');
        } catch (\DomainException) {
            // transición inválida rechazada
        }

        $this->assertSame(EstadoFactura::ANULADA, $factura->fresh()->estado);
    }

    // =====================================================================
    // Helpers
    // =====================================================================

    /**
     * Datos válidos para crear una factura de 2 unidades de un producto de 100 con 7% ITBMS.
     */
    protected function datosParaCrear(): array
    {
        return [
            'cliente_id' => Cliente::factory()->create()->id,
            'vendedor_id' => User::factory()->create()->id,
            'fecha_emision' => '2026-08-19',
            'fecha_vencimiento' => null,
            'descuento_porcentaje' => 0,
            'notas' => null,
            'items' => [
                [
                    'producto_id' => null,
                    'descripcion' => 'Producto A',
                    'cantidad' => 2,
                    'precio_unitario' => 100,
                    'descuento_porcentaje' => 0,
                    'impuesto_porcentaje' => 7,
                    'impuesto_nombre' => 'ITBMS',
                ],
            ],
        ];
    }
}
