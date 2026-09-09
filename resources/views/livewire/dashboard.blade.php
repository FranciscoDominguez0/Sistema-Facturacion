<div>
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Dashboard', 'url' => route('dashboard')]
        ]" />
    @endsection

<div class="space-y-6 max-w-[1600px] mx-auto w-full -mt-2">

      <!-- Header de Página: Título, Rango y Acción Principal -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
            Resumen del Dashboard
            
          </h1>
          <p class="text-xs text-slate-500 mt-1">Métricas de rendimiento en tiempo real</p>
        </div>

        <div class="flex items-center gap-3">
          <!-- Selector de rango de fechas -->
          <div class="inline-flex bg-white p-1 rounded-xl border border-slate-200 shadow-sm text-xs font-medium text-slate-600">
            <button wire:click="cambiarPeriodo('7d')" class="px-3 py-1.5 rounded-lg transition-colors {{ $periodo === '7d' ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">Últimos 7 días</button>
            <button wire:click="cambiarPeriodo('30d')" class="px-3 py-1.5 rounded-lg transition-colors {{ $periodo === '30d' ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">Últimos 30 días</button>
            <button wire:click="cambiarPeriodo('mes')" class="px-3 py-1.5 rounded-lg transition-colors {{ $periodo === 'mes' ? 'bg-slate-900 text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">Este mes</button>
            <button class="px-2.5 py-1.5 rounded-lg text-slate-400 hover:text-slate-700 flex items-center gap-1">
              <span class="material-symbols-outlined text-sm">calendar_month</span>
            </button>
          </div>

          <!-- Botón Nueva Factura -->
          <a href="{{ route('facturas.crear') }}" wire:navigate class="inline-flex items-center gap-2 bg-sovereign-blue text-white font-semibold text-xs px-4 py-2.5 rounded-xl hover:bg-slate-800 active:scale-[0.98] shadow-sm transition-all">
            <span class="material-symbols-outlined text-base">add</span>
            <span>Nueva Factura</span>
          </a>
        </div>
      </div>

      <!-- Fila de KPIs (4 Tarjetas con Sparkline) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- KPI 1: Ventas Totales -->
        <div class="bg-white rounded-card p-5 border border-slate-200/80 shadow-subtle relative overflow-hidden flex flex-col justify-between group hover:border-emerald-200 transition-all">
          <div class="relative z-10">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-bold tracking-wider text-slate-500 uppercase">Ventas Totales</span>
              <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                @if($ventasCrecimiento >= 0)<span class="material-symbols-outlined text-xs">trending_up</span> +{{ number_format($ventasCrecimiento, 1) }}%@else<span class="material-symbols-outlined text-xs">trending_down</span> {{ number_format($ventasCrecimiento, 1) }}%@endif
              </span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 mt-2 tracking-tight">${{ number_format($ventasTotales, 2) }}</div>
            
          </div>
          <div id="sparkVentas" class="absolute bottom-0 left-0 right-0 h-16 pointer-events-none opacity-40 group-hover:opacity-100 transition-opacity" wire:ignore></div>
        </div>

        <!-- KPI 2: Total de Facturas -->
        <div class="bg-white rounded-card p-5 border border-slate-200/80 shadow-subtle relative overflow-hidden flex flex-col justify-between group hover:border-emerald-200 transition-all">
          <div class="relative z-10">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-bold tracking-wider text-slate-500 uppercase">Total de Facturas</span>
              <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                @if($facturasCrecimiento >= 0)<span class="material-symbols-outlined text-xs">trending_up</span> +{{ number_format($facturasCrecimiento, 1) }}%@else<span class="material-symbols-outlined text-xs">trending_down</span> {{ number_format($facturasCrecimiento, 1) }}%@endif
              </span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 mt-2 tracking-tight">{{ $totalFacturas }}</div>
            
          </div>
          <div id="sparkFacturas" class="absolute bottom-0 left-0 right-0 h-16 pointer-events-none opacity-40 group-hover:opacity-100 transition-opacity" wire:ignore></div>
        </div>

        <!-- KPI 3: Nuevos Clientes -->
        <div class="bg-white rounded-card p-5 border border-slate-200/80 shadow-subtle relative overflow-hidden flex flex-col justify-between group hover:border-emerald-200 transition-all">
          <div class="relative z-10">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-bold tracking-wider text-slate-500 uppercase">Nuevos Clientes</span>
              <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                @if($clientesCrecimiento >= 0)<span class="material-symbols-outlined text-xs">trending_up</span> +{{ number_format($clientesCrecimiento, 1) }}%@else<span class="material-symbols-outlined text-xs">trending_down</span> {{ number_format($clientesCrecimiento, 1) }}%@endif
              </span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 mt-2 tracking-tight">{{ $nuevosClientes }}</div>
            
          </div>
          <div id="sparkClientes" class="absolute bottom-0 left-0 right-0 h-16 pointer-events-none opacity-40 group-hover:opacity-100 transition-opacity" wire:ignore></div>
        </div>

        <!-- KPI 4: Gastos del Periodo -->
        <div class="bg-white rounded-card p-5 border border-slate-200/80 shadow-subtle relative overflow-hidden flex flex-col justify-between group hover:border-rose-200 transition-all">
          <div class="relative z-10">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-bold tracking-wider text-slate-500 uppercase">Gastos del Periodo</span>
              <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold {{ $gastosCrecimiento > 0 ? 'text-rose-600 bg-rose-50 border-rose-100' : 'text-emerald-600 bg-emerald-50 border-emerald-100' }} px-2 py-0.5 rounded-full border">
                @if($gastosCrecimiento > 0)<span class="material-symbols-outlined text-xs">trending_up</span> +{{ number_format($gastosCrecimiento, 1) }}%@elseif($gastosCrecimiento < 0)<span class="material-symbols-outlined text-xs">trending_down</span> {{ number_format($gastosCrecimiento, 1) }}%@else<span class="material-symbols-outlined text-xs">trending_flat</span> 0%@endif
              </span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 mt-2 tracking-tight">${{ number_format($gastosTotales, 2) }}</div>
            
          </div>
          <div id="sparkGastos" class="absolute bottom-0 left-0 right-0 h-16 pointer-events-none opacity-40 group-hover:opacity-100 transition-opacity" wire:ignore></div>
        </div>

      </div>

      <!-- Fila 2: Gráfico Principal de Ventas (2/3) + Actividad Reciente (1/3) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Panel Gráfico "Resumen de Ventas" (2 de 3 columnas) -->
        <div class="lg:col-span-2 bg-white rounded-card p-6 border border-slate-200/80 shadow-subtle flex flex-col justify-between">
          <div>
            <div class="flex items-start justify-between pb-6">
              <h2 class="text-lg font-bold text-slate-900 tracking-tight">Resumen de Ventas</h2>
              <!-- Selector con ícono -->
              <div class="relative" x-data="{ open: false }">
                  <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 border border-slate-200 rounded-md text-[11px] font-semibold text-slate-600 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-sm">calendar_month</span>
                    <span>Últimos {{ $periodoGrafico === '12M' ? '12 Meses' : ($periodoGrafico === 'anio' ? 'Este Año' : '6 Meses') }}</span>
                  </button>
                  <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-1 w-32 bg-white border border-slate-200 rounded-md shadow-lg z-10 py-1" style="display: none;">
                      <button wire:click="cambiarPeriodoGrafico('6M')" @click="open = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 text-slate-700">Últimos 6 Meses</button>
                      <button wire:click="cambiarPeriodoGrafico('12M')" @click="open = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 text-slate-700">Últimos 12 Meses</button>
                      <button wire:click="cambiarPeriodoGrafico('anio')" @click="open = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 text-slate-700">Este Año</button>
                  </div>
              </div>
            </div>

            <!-- Leyenda y Totales -->
            <div class="flex items-center gap-8 mb-2">
              <div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 mb-1">
                  <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Ingresos
                </div>
                <div class="text-xl font-bold text-slate-900">${{ number_format($totalIngresosGrafico >= 1000 ? $totalIngresosGrafico / 1000 : $totalIngresosGrafico, 1) }}{{ $totalIngresosGrafico >= 1000 ? 'k' : '' }}</div>
              </div>
              <div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 mb-1">
                  <span class="w-2 h-2 rounded-full bg-slate-400"></span> Pedidos
                </div>
                <div class="text-xl font-bold text-slate-900">{{ number_format($totalPedidosGrafico >= 1000 ? $totalPedidosGrafico / 1000 : $totalPedidosGrafico, 1) }}{{ $totalPedidosGrafico >= 1000 ? 'k' : '' }}</div>
              </div>
            </div>

            <div class="mt-2 relative h-64 w-full" wire:ignore>
              <div id="chart"></div>
            </div>

          </div>
        </div>

          <!-- Panel "Actividad Reciente" (1 de 3 columnas) -->
        <div class="bg-white rounded-card p-6 border border-slate-200/80 shadow-subtle flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
              <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Actividad Reciente</h2>
                <p class="text-xs text-slate-500">Últimos movimientos de facturación</p>
              </div>
              <span class="material-symbols-outlined text-slate-400 text-lg">history</span>
            </div>

            <!-- Lista vertical de comprobantes -->
            <div class="mt-4 space-y-3.5">

              
              @forelse($facturasRecientes as $factura)
              <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                    <span class="material-symbols-outlined text-lg">description</span>
                  </div>
                  <div>
                    <div class="text-xs font-bold text-slate-900">Factura #{{ $factura->numero_factura }}</div>
                    <div class="text-[11px] text-slate-400 flex items-center gap-1">
                      <span>{{ $factura->cliente->nombre }}</span>
                      <span>•</span>
                      <span>{{ $factura->fecha_emision->format('d M, Y') }}</span>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-xs font-bold text-slate-900">${{ number_format($factura->total, 2) }}</div>
                  <span class="inline-block text-[10px] font-semibold text-emerald-600">{{ $factura->estado->value }}</span>
                </div>
              </div>
              @empty
              <p class="text-xs text-slate-500 py-4 text-center">No hay facturas recientes</p>
              @endforelse

            </div>
          </div>
          <a href="#" class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
            <span>Ver toda la bitácora</span>
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </a>
        </div>

      </div>

      <!-- Fila 3: Tabla "Gestión de Facturas" (Ancho Completo) -->
      <div class="bg-white rounded-card border border-slate-200/80 shadow-subtle overflow-hidden">
        <div class="p-6 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100">
          <div>
            <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
              Gestión de Facturas
              <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full font-semibold">142 registros</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Control y emisión en vivo de comprobantes fiscales con correlativos protegidos</p>
          </div>

          <div class="flex items-center gap-3">
            <div class="relative">
              <span class="material-symbols-outlined absolute left-2.5 top-2 text-slate-400 text-base">filter_list</span>
              <select wire:model.live="filtroEstado" class="bg-slate-50 border border-slate-200 text-xs text-slate-700 rounded-lg pl-8 pr-6 py-1.5 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="">Todos los estados</option>
                @foreach(\App\Enums\EstadoFactura::cases() as $estado)
                  <option value="{{ $estado->value }}">{{ $estado->value }}</option>
                @endforeach
              </select>
            </div>
            <a href="#" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
              Ver todas <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50/75 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
              <tr>
                <th class="py-3.5 px-6">N° Factura</th>
                <th class="py-3.5 px-6">Cliente</th>
                <th class="py-3.5 px-6">Fecha Emisión</th>
                <th class="py-3.5 px-6">Vendedor</th>
                <th class="py-3.5 px-6">Estado</th>
                <th class="py-3.5 px-6 text-right">Total</th>
                <th class="py-3.5 px-6 text-center">Acción</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">

              
              @forelse($facturasRecientes as $factura)
              <tr class="hover:bg-slate-50/70 transition-colors group">
                <td class="py-3.5 px-6 font-bold text-slate-900 flex items-center gap-2">
                  <span class="material-symbols-outlined text-emerald-600 text-base">receipt</span>
                  {{ $factura->numero_factura }}
                </td>
                <td class="py-3.5 px-6 font-medium text-slate-900">
                  {{ $factura->cliente->nombre }}
                </td>
                <td class="py-3.5 px-6 text-slate-500">{{ $factura->fecha_emision->format('d M Y') }}</td>
                <td class="py-3.5 px-6 text-slate-600">{{ $factura->vendedor->name }}</td>
                <td class="py-3.5 px-6">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $factura->estado->value }}
                  </span>
                </td>
                <td class="py-3.5 px-6 text-right font-bold text-slate-900">${{ number_format($factura->total, 2) }}</td>
                <td class="py-3.5 px-6 text-center">
                  <a href="{{ route('facturas.show', $factura) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 hover:underline">Ver / Gestionar</a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="py-8 text-center text-slate-500">No hay facturas recientes</td>
              </tr>
              @endforelse

            </tbody>
          </table>
        </div>
      </div>

      <!-- Fila 4: Bloque "Productos Más Vendidos" + "Gastos Recientes" (50% / 50%) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Tabla: Productos Más Vendidos (7 de 12 cols) -->
        <div class="lg:col-span-7 bg-white rounded-card p-6 border border-slate-200/80 shadow-subtle flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
              <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Productos Más Vendidos</h2>
                <p class="text-xs text-slate-500">Top 5 por volumen de unidades facturadas</p>
              </div>
              <a href="#" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                Ir al catálogo <span class="material-symbols-outlined text-sm">arrow_forward</span>
              </a>
            </div>

            <div class="overflow-x-auto mt-3">
              <table class="w-full text-left text-xs">
                <thead class="text-slate-400 font-bold text-[10px] uppercase tracking-wider border-b border-slate-100">
                  <tr>
                    <th class="py-2.5 px-2">Producto</th>
                    <th class="py-2.5 px-2">Tipo</th>
                    <th class="py-2.5 px-2 text-right">Precio</th>
                    <th class="py-2.5 px-2 text-center">Uds.</th>
                    <th class="py-2.5 px-2 text-right">Total Ventas</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  
                  
                  @forelse($productosMasVendidos as $prod)
                  <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3 px-2 flex items-center gap-3">
                      <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-600 shrink-0 overflow-hidden">
                        @if($prod->imagen_path)
                          <img src="{{ Storage::url($prod->imagen_path) }}" alt="{{ $prod->descripcion }}" class="w-full h-full object-cover">
                        @else
                          <span class="material-symbols-outlined text-lg">inventory_2</span>
                        @endif
                      </div>
                      <div>
                        <div class="font-bold text-slate-900">{{ $prod->descripcion }}</div>
                      </div>
                    </td>
                    <td class="py-3 px-2">
                      <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">General</span>
                    </td>
                    <td class="py-3 px-2 text-right text-slate-700 font-medium">${{ number_format($prod->precio_unitario, 2) }}</td>
                    <td class="py-3 px-2 text-center font-bold text-slate-900">{{ $prod->total_cantidad }}</td>
                    <td class="py-3 px-2 text-right font-extrabold text-emerald-600">${{ number_format($prod->total_ventas, 2) }}</td>
                  </tr>
                  @empty
                  <tr><td colspan="5" class="py-8 text-center text-slate-500">No hay productos vendidos en este periodo</td></tr>
                  @endforelse

                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Panel: Gastos Recientes & Balance Financiero (5 de 12 cols) -->
        <div class="lg:col-span-5 bg-white rounded-card p-6 border border-slate-200/80 shadow-subtle flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
              <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Gastos Recientes</h2>
                <p class="text-xs text-slate-500">Egresos operativos del periodo</p>
              </div>
              <!-- Mini indicador de balance financiero -->
              <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Egresos</span>
                <span class="text-xs font-extrabold text-rose-600">-${{ number_format($gastosTotales, 2) }}</span>
              </div>
            </div>

            <!-- Resumen de Balance (Ventas - Gastos) -->
            <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                  <span class="material-symbols-outlined text-sm">account_balance_wallet</span>
                </div>
                <div>
                  <div class="text-[11px] font-bold text-slate-800">Margen Operativo Neto</div>
                  <div class="text-[10px] text-slate-400">Ventas (${{ number_format($ventasTotales, 2) }}) − Gastos (${{ number_format($gastosTotales, 2) }})</div>
                </div>
              </div>
              <div class="text-right">
                <span class="text-sm font-extrabold text-emerald-600">${{ number_format($ventasTotales - $gastosTotales, 2) }}</span>
                @if($ventasTotales > 0)
                  <span class="block text-[10px] text-slate-400 font-medium">{{ number_format((($ventasTotales - $gastosTotales) / $ventasTotales) * 100, 1) }}% Margen</span>
                @else
                  <span class="block text-[10px] text-slate-400 font-medium">0% Margen</span>
                @endif
              </div>
            </div>

            <!-- Lista de Gastos -->
            <div class="mt-4 space-y-3">
              
              
              @forelse($gastosRecientes as $gasto)
              <div class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-base">payments</span>
                  </div>
                  <div>
                    <div class="text-xs font-bold text-slate-900">{{ $gasto->descripcion }}</div>
                    <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                      <span class="inline-block px-1.5 py-0.2 rounded bg-rose-50 text-rose-700 font-semibold">{{ $gasto->categoria }}</span>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-xs font-bold text-slate-900">-${{ number_format($gasto->monto, 2) }}</div>
                  <div class="text-[10px] text-slate-400">{{ $gasto->fecha->format('d M Y') }}</div>
                </div>
              </div>
              @empty
              <p class="text-xs text-slate-500 py-4 text-center">No hay gastos recientes</p>
              @endforelse

            </div>
          </div>

          <a href="#" class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-center gap-1 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            <span>Ver módulo de gastos completo</span>
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </a>
        </div>

      </div>

    </div>

@script
<script>
    const obtenerDatos = () => ({
        sparklineVentas: $wire.sparklineVentas,
        sparklineFacturas: $wire.sparklineFacturas,
        sparklineClientes: $wire.sparklineClientes,
        sparklineGastos: $wire.sparklineGastos,
        chartData: $wire.chartData,
    });

    const iniciarGraficas = () => {
        if (!window.dashboardCharts) {
            setTimeout(iniciarGraficas, 100);
            return;
        }
        window.dashboardCharts.iniciar(obtenerDatos());
    };

    iniciarGraficas();

    // Redibuja tras cada actualización de Livewire (filtros, etc.)
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => iniciarGraficas());
    });
</script>
@endscript
</div>
