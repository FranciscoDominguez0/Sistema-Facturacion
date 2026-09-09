import re

filepath = 'resources/views/livewire/dashboard.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    html = f.read()

header_regex = r'<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">.*?<div class="mt-6 relative h-64 w-full" wire:ignore>'

new_header = """<div class="flex items-start justify-between pb-6">
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

            <div class="mt-2 relative h-64 w-full" wire:ignore>"""

html = re.sub(header_regex, new_header, html, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(html)
