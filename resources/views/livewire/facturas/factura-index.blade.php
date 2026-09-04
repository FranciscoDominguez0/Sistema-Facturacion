<div>
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Facturas']
        ]" />
    @endsection

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Listado de Facturas</h2>
            <p class="text-slate-500 text-sm mt-1">Gestión del historial de ventas facturadas.</p>
        </div>
        @can('facturas.crear')
            <a href="{{ route('facturas.crear') }}" class="inline-flex items-center justify-center px-4 py-2 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors shadow-sm" wire:navigate>
                <span class="material-symbols-outlined text-[20px] mr-2">add</span>
                Nueva Venta
            </a>
        @endcan
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-slate-400 text-lg">search</span>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" class="pl-10 block w-full rounded-lg border-slate-300 shadow-sm focus:border-sovereign-blue focus:ring-sovereign-blue sm:text-sm text-slate-900 placeholder-slate-400" placeholder="Buscar por cliente o nº factura...">
        </div>
        
        <div class="w-full sm:w-auto flex items-center gap-2">
            <span class="text-sm text-slate-500 font-medium">Estado:</span>
            <select wire:model.live="filtroEstado" class="block w-full sm:w-48 rounded-lg border-slate-300 shadow-sm focus:border-sovereign-blue focus:ring-sovereign-blue sm:text-sm text-slate-900">
                <option value="Todos">Todos</option>
                @foreach(\App\Enums\EstadoFactura::cases() as $estado)
                    <option value="{{ $estado->value }}">{{ $estado->value }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="px-6 py-4">Nº Factura</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Emisión</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($facturas as $factura)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $factura->numero_factura }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-800 font-medium">{{ $factura->cliente->nombre }}</div>
                            @if($factura->cliente->identificacion_fiscal)
                                <div class="text-xs text-slate-500 mt-0.5">ID: {{ $factura->cliente->identificacion_fiscal }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $factura->fecha_emision->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-slate-800">
                            ${{ number_format($factura->total, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeClasses = match($factura->estado->value) {
                                    'Pagada' => 'bg-green-100 text-green-800 border-green-200',
                                    'Anulada' => 'bg-red-100 text-red-800 border-red-200',
                                    default => 'bg-amber-100 text-amber-800 border-amber-200',
                                };
                            @endphp
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full border {{ $badgeClasses }}">
                                {{ $factura->estado->value }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('facturas.show', $factura->id) }}" class="text-sovereign-blue hover:text-slate-900 font-medium text-sm transition-colors" wire:navigate>
                                Ver Detalle
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                <span class="material-symbols-outlined text-3xl text-slate-400">receipt_long</span>
                            </div>
                            <h3 class="text-lg font-medium text-slate-900 mb-1">No hay facturas</h3>
                            <p class="text-slate-500 text-sm">
                                @if(empty($search) && $filtroEstado === 'Todos')
                                    Comienza creando tu primera factura de venta.
                                @else
                                    No se encontraron resultados para tu búsqueda.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($facturas->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $facturas->links() }}
            </div>
        @endif
    </div>
</div>
