import re

filepath = 'resources/views/livewire/dashboard.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    html = f.read()

# Replace metrics
html = re.sub(r'>\$-3,420\.00<', r'>-${{ number_format($gastosTotales, 2) }}<', html)
html = re.sub(r'Ventas \(\$24\.8k\) − Gastos \(\$3\.4k\)', r'Ventas (${{ number_format($ventasTotales, 2) }}) − Gastos (${{ number_format($gastosTotales, 2) }})', html)
html = re.sub(r'\+\$21,430\.00', r'${{ number_format($ventasTotales - $gastosTotales, 2) }}', html)
html = re.sub(r'Mes más alto</div>\s*<div class="text-sm font-bold text-slate-800 mt-0\.5">Septiembre \(\$24\.8k\)</div>', r'Mes más alto</div>\n<div class="text-sm font-bold text-slate-800 mt-0.5">{{ $mesMasAlto }} (${{ number_format($mesMasAltoValor, 2) }})</div>', html)
html = re.sub(r'94\.2% a tiempo', r'{{ number_format($tasaCobro, 1) }}% pagadas', html)
html = re.sub(r'\$78,200\.00', r'${{ number_format($proyeccionTrimestre, 2) }}', html)

# Replace the Chart SVG with ApexCharts container and script
chart_replacement = """<div class="mt-6 relative h-64 w-full" wire:ignore>
              <div id="chart"></div>
            </div>
"""
html = re.sub(r'<!-- Gráfico Vectorial Interactivo SVG Estilo Stripe / Linear -->.*?</div>\s*<!-- Eje X \(Meses\) -->', chart_replacement + '\n            <!-- Eje X (Meses) -->', html, flags=re.DOTALL)
html = re.sub(r'<!-- Eje X \(Meses\) -->.*?</div>', '', html, flags=re.DOTALL) # Remove static X axis

# Replace "Actividad Reciente" items
actividad_replacement = """
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
"""
html = re.sub(r'<!-- Item 1: Carlos Fernández \(FAC-260008\) -->.*?</div>\s*</div>\s*<a href="#"', actividad_replacement + '\n            </div>\n          </div>\n          <a href="#"', html, flags=re.DOTALL)


# Replace Table Facturas
table_facturas_replacement = """
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
"""
html = re.sub(r'<!-- Fila 1: FAC-260008 \(Pagada\) -->.*?</tbody>', table_facturas_replacement + '\n            </tbody>', html, flags=re.DOTALL)


# Replace Productos
table_productos = """
                  @forelse($productosMasVendidos as $prod)
                  <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3 px-2 flex items-center gap-3">
                      <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-600 shrink-0">
                        <span class="material-symbols-outlined text-lg">inventory_2</span>
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
"""
html = re.sub(r'<!-- Prod 1 -->.*?</tbody>', table_productos + '\n                </tbody>', html, flags=re.DOTALL)

# Replace Gastos
gastos_replacement = """
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
"""
html = re.sub(r'<!-- Gasto 1: Software -->.*?</div>\s*</div>\s*<a href="#" class="mt-4 pt-3', gastos_replacement + '\n            </div>\n          </div>\n\n          <a href="#" class="mt-4 pt-3', html, flags=re.DOTALL)

script = """
@script
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let options = {
        series: [{
            name: 'Ingresos',
            type: 'area',
            data: $wire.chartData.ingresos
        }, {
            name: 'Facturas Emitidas',
            type: 'line',
            data: $wire.chartData.volumen
        }],
        chart: {
            height: 250,
            type: 'line',
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        stroke: {
            curve: 'smooth',
            width: [3, 2],
            dashArray: [0, 5]
        },
        fill: {
            type: ['gradient', 'solid'],
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100]
            }
        },
        colors: ['#10B981', '#94A3B8'],
        labels: $wire.chartData.meses,
        xaxis: {
            tooltip: { enabled: false },
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: '#94A3B8',
                    fontSize: '11px',
                    fontWeight: 600
                }
            }
        },
        yaxis: [{
            labels: {
                formatter: function (value) {
                    return "$" + value.toLocaleString();
                },
                style: { colors: '#94A3B8', fontSize: '11px', fontWeight: 500 }
            }
        }, {
            opposite: true,
            labels: { show: false }
        }],
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } }
        },
        legend: { show: false },
        dataLabels: { enabled: false }
    };

    let chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>
@endscript
</div>
"""

html = re.sub(r'</div>$', script, html)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(html)
