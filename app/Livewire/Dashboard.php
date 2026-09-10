<?php

namespace App\Livewire;

use App\Enums\EstadoFactura;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\Gasto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    // Periodo de las métricas: '7d' | '30d' | 'mes'
    public string $periodo = 'mes';

    // Periodo del gráfico principal: '6M' | '12M' | 'anio'
    public string $periodoGrafico = '6M';

    // Filtro de estado para la tabla de facturas recientes
    public string $filtroEstado = '';

    public $ventasTotales = 0;

    public $totalFacturas = 0;

    public $nuevosClientes = 0;

    public $mesMasAlto = '';

    public $mesMasAltoValor = 0;

    public $tasaCobro = 0;

    public $proyeccionTrimestre = 0;

    public $mesActual = '';

    public $ventasCrecimiento = 0;

    public $facturasCrecimiento = 0;

    public $clientesCrecimiento = 0;

    public $gastosCrecimiento = 0;

    public $sparklineVentas = [];

    public $sparklineFacturas = [];

    public $sparklineClientes = [];

    public $sparklineGastos = [];

    public $totalIngresosGrafico = 0;

    public $totalPedidosGrafico = 0;

    public $chartData = [];

    public $facturasRecientes = [];

    public $productosMasVendidos = [];

    public $gastosRecientes = [];

    public $gastosTotales = 0;

    public function mount()
    {
        $this->cargarDatos();
    }

    public function cambiarPeriodo(string $periodo)
    {
        $this->periodo = $periodo;
        $this->cargarDatos();
    }

    public function cambiarPeriodoGrafico(string $periodo)
    {
        $this->periodoGrafico = $periodo;
        $this->cargarDatosGrafico();
    }

    public function updatedFiltroEstado()
    {
        [$inicio, $fin] = $this->rangoActual();
        $this->cargarFacturasRecientes($inicio, $fin);
    }

    private function cargarDatos()
    {
        [$inicio, $fin] = $this->rangoActual();
        [$inicioAnterior, $finAnterior] = $this->rangoAnterior();

        $this->mesActual = Carbon::now()->translatedFormat('M Y');

        // KPIs del periodo seleccionado (una consulta agrupada en lugar de varias sueltas)
        $resumen = $this->resumenFacturas($inicio, $fin);
        $resumenAnterior = $this->resumenFacturas($inicioAnterior, $finAnterior);

        $this->ventasTotales = $resumen['ventas'];
        $this->totalFacturas = $resumen['total'];

        $this->nuevosClientes = Cliente::whereBetween('created_at', [$inicio, $fin])->count();

        // Crecimiento comparado con el periodo anterior equivalente
        $this->ventasCrecimiento = $resumenAnterior['ventas'] > 0 ? (($this->ventasTotales - $resumenAnterior['ventas']) / $resumenAnterior['ventas']) * 100 : 0;

        $this->facturasCrecimiento = $resumenAnterior['total'] > 0 ? (($this->totalFacturas - $resumenAnterior['total']) / $resumenAnterior['total']) * 100 : 0;

        $clientesAnterior = Cliente::whereBetween('created_at', [$inicioAnterior, $finAnterior])->count();
        $this->clientesCrecimiento = $clientesAnterior > 0 ? (($this->nuevosClientes - $clientesAnterior) / $clientesAnterior) * 100 : 0;

        // Paneles secundarios del periodo
        $this->cargarFacturasRecientes($inicio, $fin);
        $this->cargarGastos($inicio, $fin, $inicioAnterior, $finAnterior);
        $this->cargarProductosMasVendidos($inicio, $fin);

        $this->cargarDatosGrafico();
        $this->calcularMetricasExtra();
    }

    private function cargarFacturasRecientes(Carbon $inicio, Carbon $fin)
    {
        $query = Factura::with(['cliente', 'vendedor'])
            ->whereBetween('fecha_emision', [$inicio, $fin])
            ->orderBy('fecha_emision', 'desc')
            ->orderBy('created_at', 'desc');

        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        $this->facturasRecientes = $query->take(6)->get();
    }

    private function cargarGastos(Carbon $inicio, Carbon $fin, Carbon $inicioAnterior, Carbon $finAnterior)
    {
        $this->gastosRecientes = Gasto::orderBy('fecha', 'desc')->take(3)->get();
        $this->gastosTotales = Gasto::whereBetween('fecha', [$inicio, $fin])->sum('monto');

        $gastosAnterior = Gasto::whereBetween('fecha', [$inicioAnterior, $finAnterior])->sum('monto');
        $this->gastosCrecimiento = $gastosAnterior > 0 ? (($this->gastosTotales - $gastosAnterior) / $gastosAnterior) * 100 : 0;
    }

    private function cargarProductosMasVendidos(Carbon $inicio, Carbon $fin)
    {
        $this->productosMasVendidos = FacturaItem::select(
            'factura_items.descripcion',
            DB::raw('SUM(factura_items.cantidad) as total_cantidad'),
            DB::raw('SUM(factura_items.subtotal_linea) as total_ventas'),
            DB::raw('MAX(factura_items.precio_unitario) as precio_unitario'),
            DB::raw('MAX(productos.imagen_path) as imagen_path'),
        )
            ->join('facturas', 'factura_items.factura_id', '=', 'facturas.id')
            ->leftJoin('productos', 'factura_items.producto_id', '=', 'productos.id')
            ->where('facturas.estado', '!=', EstadoFactura::ANULADA->value)
            ->whereBetween('facturas.fecha_emision', [$inicio, $fin])
            ->groupBy('factura_items.descripcion')
            ->orderBy('total_cantidad', 'desc')
            ->take(5)
            ->get();
    }

    private function cargarDatosGrafico()
    {
        $meses = [];
        $ingresos = [];
        $volumen = [];
        $this->mesMasAltoValor = 0;
        $this->mesMasAlto = '';

        $ahora = Carbon::now();

        $primerMes = $ahora->copy()->startOfMonth();
        if ($this->periodoGrafico === 'anio') {
            $totalMeses = $ahora->month;
            $primerMes = $ahora->copy()->startOfYear();
        } else {
            $cantidad = $this->periodoGrafico === '12M' ? 12 : 6;
            $totalMeses = $cantidad;
            $primerMes = $ahora->copy()->startOfMonth()->subMonths($cantidad - 1);
        }

        // Una sola consulta agrupada por mes (antes: 2 consultas por mes).
        $filaMes = "to_char(fecha_emision, 'YYYY-MM')";
        $resultados = Factura::where('estado', '!=', EstadoFactura::ANULADA->value)
            ->whereBetween('fecha_emision', [$primerMes->copy()->startOfMonth(), $ahora->copy()->endOfMonth()])
            ->selectRaw("{$filaMes} as mes")
            ->selectRaw('SUM(total) as ingresos')
            ->selectRaw('COUNT(*) as volumen')
            ->groupByRaw($filaMes)
            ->orderByRaw($filaMes)
            ->get()
            ->keyBy('mes');

        for ($i = 0; $i < $totalMeses; $i++) {
            $mes = $primerMes->copy()->addMonths($i);
            $fila = $resultados->get($mes->format('Y-m'));

            $meses[] = ucfirst($mes->translatedFormat('M'));

            $totalMes = $fila ? round((float) $fila->ingresos, 2) : 0;
            $ingresos[] = $totalMes;
            $volumen[] = $fila ? (int) $fila->volumen : 0;

            if ($totalMes > $this->mesMasAltoValor) {
                $this->mesMasAltoValor = $totalMes;
                $this->mesMasAlto = ucfirst($mes->translatedFormat('F'));
            }
        }

        $this->chartData = [
            'meses' => $meses,
            'ingresos' => $ingresos,
            'volumen' => $volumen,
        ];

        $this->totalIngresosGrafico = array_sum($ingresos);
        $this->totalPedidosGrafico = array_sum($volumen);
    }

    private function calcularMetricasExtra()
    {
        [$inicio, $fin] = $this->rangoActual();

        // Tasa de cobro con una sola consulta agrupada por estado.
        $facturasPorEstado = Factura::whereBetween('fecha_emision', [$inicio, $fin])
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->map(fn ($total) => (int) $total);

        $totalNoAnuladas = $facturasPorEstado->except([EstadoFactura::ANULADA->value])->sum();
        $totalPagadas = $facturasPorEstado[EstadoFactura::PAGADA->value] ?? 0;

        $this->tasaCobro = $totalNoAnuladas > 0 ? ($totalPagadas / $totalNoAnuladas) * 100 : 0;

        $this->proyeccionTrimestre = $this->ventasTotales * 3;

        // Sparklines - últimos 14 días (una consulta agrupada por día y serie,
        // antes: 4 consultas por día).
        $inicioSpark = Carbon::now()->subDays(13)->startOfDay();
        $finSpark = Carbon::now()->endOfDay();

        $ventasPorDia = Factura::where('estado', '!=', EstadoFactura::ANULADA->value)
            ->whereBetween('fecha_emision', [$inicioSpark, $finSpark])
            ->selectRaw('fecha_emision::date as dia, SUM(total) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia')
            ->map(fn ($total) => round((float) $total, 2));

        $facturasPorDia = Factura::whereBetween('fecha_emision', [$inicioSpark, $finSpark])
            ->selectRaw('fecha_emision::date as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia')
            ->map(fn ($total) => (int) $total);

        $clientesPorDia = Cliente::whereBetween('created_at', [$inicioSpark, $finSpark])
            ->selectRaw('created_at::date as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia')
            ->map(fn ($total) => (int) $total);

        $gastosPorDia = Gasto::whereBetween('fecha', [$inicioSpark, $finSpark])
            ->selectRaw('fecha::date as dia, SUM(monto) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia')
            ->map(fn ($total) => round((float) $total, 2));

        $this->sparklineVentas = [];
        $this->sparklineFacturas = [];
        $this->sparklineClientes = [];
        $this->sparklineGastos = [];

        for ($i = 13; $i >= 0; $i--) {
            $dia = Carbon::now()->subDays($i)->format('Y-m-d');

            $this->sparklineVentas[] = $ventasPorDia[$dia] ?? 0;
            $this->sparklineFacturas[] = $facturasPorDia[$dia] ?? 0;
            $this->sparklineClientes[] = $clientesPorDia[$dia] ?? 0;
            $this->sparklineGastos[] = $gastosPorDia[$dia] ?? 0;
        }
    }

    /**
     * Ventas (sin anuladas) y total de facturas de un rango en una sola consulta.
     *
     * @return array{ventas: float, total: int}
     */
    private function resumenFacturas(Carbon $inicio, Carbon $fin): array
    {
        $resumen = Factura::whereBetween('fecha_emision', [$inicio, $fin])
            ->selectRaw('SUM(CASE WHEN estado <> ? THEN total ELSE 0 END) as ventas', [EstadoFactura::ANULADA->value])
            ->selectRaw('COUNT(*) as total_facturas')
            ->first();

        return [
            'ventas' => round((float) ($resumen->ventas ?? 0), 2),
            'total' => (int) ($resumen->total_facturas ?? 0),
        ];
    }

    /**
     * Rango de fechas del periodo seleccionado.
     *
     * @return array{Carbon, Carbon}
     */
    private function rangoActual(): array
    {
        $ahora = Carbon::now();

        return match ($this->periodo) {
            '7d' => [$ahora->copy()->startOfDay()->subDays(6), $ahora->copy()->endOfDay()],
            '30d' => [$ahora->copy()->startOfDay()->subDays(29), $ahora->copy()->endOfDay()],
            default => [$ahora->copy()->startOfMonth(), $ahora->copy()->endOfMonth()],
        };
    }

    /**
     * Rango equivalente anterior, para calcular crecimiento.
     *
     * @return array{Carbon, Carbon}
     */
    private function rangoAnterior(): array
    {
        $ahora = Carbon::now();

        return match ($this->periodo) {
            '7d' => [$ahora->copy()->startOfDay()->subDays(13), $ahora->copy()->endOfDay()->subDays(7)],
            '30d' => [$ahora->copy()->startOfDay()->subDays(59), $ahora->copy()->endOfDay()->subDays(30)],
            default => [$ahora->copy()->subMonth()->startOfMonth(), $ahora->copy()->subMonth()->endOfMonth()],
        };
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
