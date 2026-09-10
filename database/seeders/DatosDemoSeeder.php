<?php

namespace Database\Seeders;

use App\Enums\EstadoFactura;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\Gasto;
use App\Models\Impuesto;
use App\Models\Producto;
use App\Models\User;
use App\Services\FacturaService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Llena la base de datos con datos de prueba para probar la aplicación:
 * 20 clientes, 2 facturas por cliente, productos, gastos y vendedores.
 *
 * Es seguro re-ejecutarlo: primero limpia los datos de prueba existentes
 * (no toca usuarios, roles ni permisos).
 */
class DatosDemoSeeder extends Seeder
{
    use WithoutModelEvents;

    private const TOTAL_CLIENTES = 20;

    private const FACTURAS_POR_CLIENTE = 2;

    private const TOTAL_PRODUCTOS = 18;

    private const TOTAL_GASTOS = 28;

    private const TOTAL_VENDEDORES = 3;

    public function run(): void
    {
        $this->limpiarDatosDePrueba();

        Empresa::actual();

        $impuestos = $this->crearImpuestos();
        $vendedores = $this->crearVendedores();

        // Cada producto recibe un impuesto al azar y un par quedan exentos para probar.
        $productos = Producto::factory()
            ->count(self::TOTAL_PRODUCTOS)
            ->create([
                'impuesto_id' => fn() => $impuestos->random()->id,
            ]);

        $productos->take(2)->each(fn(Producto $producto) => $producto->update(['impuesto_id' => null]));
        $productos->load('impuesto');

        $clientes = Cliente::factory()->count(self::TOTAL_CLIENTES)->create();

        $this->crearFacturas($clientes, $vendedores, $productos);
        $this->crearGastos($vendedores);
    }

    /**
     * Crea los impuestos de prueba (7%, 10%, 15% y 20%). Idempotente por nombre.
     *
     * @return Collection<int, Impuesto>
     */
    private function crearImpuestos()
    {
        return collect([
            ['nombre' => 'ITBMS', 'porcentaje' => 7],
            ['nombre' => 'Impuesto 10', 'porcentaje' => 10],
            ['nombre' => 'Impuesto 15', 'porcentaje' => 15],
            ['nombre' => 'Impuesto 20', 'porcentaje' => 20],
        ])->map(fn(array $datos) => Impuesto::updateOrCreate(
                ['nombre' => $datos['nombre']],
                ['porcentaje' => $datos['porcentaje'], 'activo' => true],
            ));
    }

    /**
     * Borra los datos de prueba anteriores y reinicia el correlativo
     * de facturas para que la numeración empiece desde FAC-000001.
     */
    private function limpiarDatosDePrueba(): void
    {
        FacturaItem::query()->delete();
        Factura::query()->delete();
        Gasto::query()->delete();
        Producto::query()->delete();
        Cliente::query()->delete();

        Empresa::query()->update(['siguiente_numero_factura' => 1]);
    }

    /**
     * Crea los usuarios vendedores con su rol. Idempotente por email.
     *
     * @return Collection<int, User>
     */
    private function crearVendedores()
    {
        $rolVendedor = Role::firstOrCreate(['name' => 'Vendedor']);

        $vendedores = collect();

        for ($i = 1; $i <= self::TOTAL_VENDEDORES; $i++) {
            $vendedor = User::updateOrCreate(
                ['email' => "vendedor{$i}@demo.com"],
                [
                    'name' => "Vendedor {$i}",
                    'password' => Hash::make('password'),
                ]
            );
            $vendedor->assignRole($rolVendedor);
            $vendedores->push($vendedor);
        }

        return $vendedores;
    }

    /**
     * Crea 2 facturas por cliente con líneas de productos reales, fechas
     * repartidas en los últimos 6 meses y estados variados.
     *
     * @param  Collection<int, Cliente>  $clientes
     * @param  Collection<int, User>  $vendedores
     * @param  Collection<int, Producto>  $productos
     */
    private function crearFacturas($clientes, $vendedores, $productos): void
    {
        $facturaService = app(FacturaService::class);

        foreach ($clientes as $cliente) {
            for ($i = 0; $i < self::FACTURAS_POR_CLIENTE; $i++) {
                $items = $this->generarItems($productos);

                $factura = $facturaService->crear([
                    'cliente_id' => $cliente->id,
                    'vendedor_id' => $vendedores->random()->id,
                    'fecha_emision' => $this->fechaAleatoriaReciente(),
                    'fecha_vencimiento' => null,
                    'descuento_porcentaje' => 0,
                    'notas' => fake()->boolean(20) ? fake()->sentence() : null,
                    'items' => $items,
                ]);

                $estado = $this->elegirEstado();
                $factura->update([
                    'estado' => $estado,
                    'fecha_vencimiento' => $estado === EstadoFactura::PENDIENTE
                        ? now()->addDays(15)->format('Y-m-d')
                        : null,
                ]);
            }
        }
    }

    /**
     * Genera entre 1 y 3 líneas de venta usando productos del catálogo.
     *
     * @param  Collection<int, Producto>  $productos
     * @return array<int, array<string, mixed>>
     */
    private function generarItems($productos): array
    {
        $items = [];

        foreach (range(1, random_int(1, 3)) as $ignorado) {
            $producto = $productos->random();
            $items[] = [
                'producto_id' => $producto->id,
                'descripcion' => $producto->nombre,
                'cantidad' => random_int(1, 5),
                'precio_unitario' => $producto->precio,
                'descuento_porcentaje' => 0,
                'aplica_impuesto' => $producto->aplica_impuesto,


            ];
        }

        return $items;
    }

    /**
     * Estado con distribución realista: mayoría pagadas, algunas pendientes
     * y un par anuladas.
     */
    private function elegirEstado(): EstadoFactura
    {
        $suerte = random_int(1, 100);

        return match (true) {
            $suerte <= 60 => EstadoFactura::PAGADA,
            $suerte <= 90 => EstadoFactura::PENDIENTE,
            default => EstadoFactura::ANULADA,
        };
    }

    /**
     * Fecha aleatoria dentro de los últimos 6 meses.
     */
    private function fechaAleatoriaReciente(): string
    {
        return now()->subDays(random_int(1, 180))->format('Y-m-d');
    }

    /**
     * Crea gastos variados repartidos en los últimos 6 meses.
     *
     * @param  Collection<int, User>  $vendedores
     */
    private function crearGastos($vendedores): void
    {
        $categorias = config('gastos.categorias');

        for ($i = 0; $i < self::TOTAL_GASTOS; $i++) {
            Gasto::create([
                'concepto' => Str::ucfirst(fake()->sentence(3)),
                'categoria' => fake()->randomElement($categorias),
                'monto' => fake()->randomFloat(2, 10, 1000),
                'fecha' => $this->fechaAleatoriaReciente(),
                'registrado_por' => $vendedores->random()->id,
                'comprobante' => fake()->boolean(40) ? fake()->numerify('FAC-####') : null,
            ]);
        }
    }
}
