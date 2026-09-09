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

        // KPIs del periodo seleccionado
        $this->ventasTotales = Factura::where('estado', '!=', EstadoFactura::ANULADA->value)
            ->whereBetween('fecha_emision', [$inicio, $fin])
            ->sum('total');

        $this->totalFacturas = Factura::whereBetween('fecha_emision', [$inicio, $fin])->count();

        $this->nuevosClientes = Cliente::whereBetween('created_at', [$inicio, $fin])->count();

        // Crecimiento comparado con el periodo anterior equivalente
        $ventasAnterior = Factura::where('estado', '!=', EstadoFactura::ANULADA->value)
            ->whereBetween('fecha_emision', [$inicioAnterior, $finAnterior])
            ->sum('total');
        $this->ventasCrecimiento = $ventasAnterior > 0 ? (($this->ventasTotales - $ventasAnterior) / $ventasAnterior) * 100 : 0;

        $facturasAnterior = Factura::whereBetween('fecha_emision', [$inicioAnterior, $finAnterior])->count();
        $this->facturasCrecimiento = $facturasAnterior > 0 ? (($this->totalFacturas - $facturasAnterior) / $facturasAnterior) * 100 : 0;

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

        if ($this->periodoGrafico === 'anio') {
            $totalMeses = $ahora->month;
            for ($i = 0; $i < $totalMeses; $i++) {
                $this->agregarMesAlGrafico($ahora->copy()->startOfYear()->addMonths($i), $meses, $ingresos, $volumen);
            }
        } else {
            $cantidad = $this->periodoGrafico === '12M' ? 12 : 6;
            for ($i = $cantidad - 1; $i >= 0; $i--) {
                $this->agregarMesAlGrafico($ahora->copy()->startOfMonth()->subMonths($i), $meses, $ingresos, $volumen);
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

    private function agregarMesAlGrafico(Carbon $mes, array &$meses, array &$ingresos, array &$volumen)
    {
        $inicio = $mes->copy()->startOfMonth();
        $fin = $mes->copy()->endOfMonth();

        $meses[] = ucfirst($mes->translatedFormat('M'));

        $totalMes = Factura::where('estado', '!=', EstadoFactura::ANULADA->value)
            ->whereBetween('fecha_emision', [$inicio, $fin])
            ->sum('total');

        $ingresos[] = round($totalMes, 2);

        $volumen[] = Factura::where('estado', '!=', EstadoFactura::ANULADA->value)
            ->whereBetween('fecha_emision', [$inicio, $fin])
            ->count();

        if ($totalMes > $this->mesMasAltoValor) {
            $this->mesMasAltoValor = $totalMes;
            $this->mesMasAlto = ucfirst($mes->translatedFormat('F'));
        }
    }

    private function calcularMetricasExtra()
    {
        [$inicio, $fin] = $this->rangoActual();

        $totalNoAnuladas = Factura::where('estado', '!=', EstadoFactura::ANULADA->value)
            ->whereBetween('fecha_emision', [$inicio, $fin])
            ->count();

        $totalPagadas = Factura::where('estado', EstadoFactura::PAGADA->value)
            ->whereBetween('fecha_emision', [$inicio, $fin])
            ->count();

        $this->tasaCobro = $totalNoAnuladas > 0 ? ($totalPagadas / $totalNoAnuladas) * 100 : 0;

        $this->proyeccionTrimestre = $this->ventasTotales * 3;

        // Sparklines - últimos 14 días
        $this->sparklineVentas = [];
        $this->sparklineFacturas = [];
        $this->sparklineClientes = [];
        $this->sparklineGastos = [];

        for ($i = 13; $i >= 0; $i--) {
            $dia = Carbon::now()->subDays($i)->format('Y-m-d');

            $ventasDia = Factura::where('estado', '!=', EstadoFactura::ANULADA->value)
                ->whereDate('fecha_emision', $dia)
                ->sum('total');

            $facturasDia = Factura::whereDate('fecha_emision', $dia)->count();

            $clientesDia = Cliente::whereDate('created_at', $dia)->count();

            $gastosDia = Gasto::whereDate('fecha', $dia)->sum('monto');

            $this->sparklineVentas[] = round($ventasDia, 2);
            $this->sparklineFacturas[] = $facturasDia;
            $this->sparklineClientes[] = $clientesDia;
            $this->sparklineGastos[] = round($gastosDia, 2);
        }
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
