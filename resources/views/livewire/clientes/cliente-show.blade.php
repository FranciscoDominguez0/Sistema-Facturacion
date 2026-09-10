<div class="w-full">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Clientes', 'url' => route('clientes')],
            ['title' => $cliente->nombre]
        ]" />
    @endsection

    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-start justify-between gap-6">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                @if($cliente->activo)
                    <span class="px-2 py-1 bg-emerald-100 text-emerald-800 font-bold text-[11px] rounded uppercase tracking-wider border border-emerald-200">Activo</span>
                @else
                    <span class="px-2 py-1 bg-slate-100 text-slate-600 font-bold text-[11px] rounded uppercase tracking-wider border border-slate-200">Inactivo</span>
                @endif
                <span class="font-mono text-sm text-slate-500">ID: {{ $cliente->identificacion ?? 'N/A' }}</span>
            </div>
            <h1 class="text-3xl font-bold text-sovereign-blue mb-4">{{ $cliente->nombre }}</h1>
            
            <div class="flex flex-wrap gap-y-2 gap-x-6">
                @if($cliente->email)
                <div class="flex items-center text-slate-500 gap-2">
                    <span class="material-symbols-outlined text-[18px]">mail</span>
                    <a class="text-sm hover:text-sovereign-blue transition-colors" href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>
                </div>
                @endif
                @if($cliente->telefono)
                <div class="flex items-center text-slate-500 gap-2">
                    <span class="material-symbols-outlined text-[18px]">call</span>
                    <a class="text-sm hover:text-sovereign-blue transition-colors" href="tel:{{ $cliente->telefono }}">{{ $cliente->telefono }}</a>
                </div>
                @endif
                @if($cliente->direccion)
                <div class="flex items-center text-slate-500 gap-2">
                    <span class="material-symbols-outlined text-[18px]">location_on</span>
                    <span class="text-sm">{{ $cliente->direccion }}</span>
                </div>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0 mt-4 md:mt-0">
            <a href="{{ route('clientes.edit', $cliente) }}" wire:navigate class="px-4 py-2 border border-sovereign-blue text-sovereign-blue bg-transparent rounded font-bold text-[11px] uppercase tracking-wider hover:bg-slate-50 transition-colors flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Editar
            </a>
            <a href="{{ route('facturas.crear', ['cliente_id' => $cliente->id]) }}" wire:navigate class="px-4 py-2 border border-sovereign-blue text-white bg-sovereign-blue rounded font-bold text-[11px] uppercase tracking-wider hover:bg-opacity-90 transition-colors flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Nueva Factura
            </a>
        </div>
    </div>

    <!-- Bento Grid Layout for Details & Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Quick Stats Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col justify-center shadow-sm">
            <h3 class="font-bold text-[11px] text-slate-500 uppercase tracking-wider mb-2">Total Facturado YTD</h3>
            <div class="text-2xl font-bold text-sovereign-blue mb-1">${{ number_format($facturas->sum('total'), 2) }}</div>
            <div class="flex items-center text-emerald-600 gap-1 text-sm font-medium mt-2">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>{{ $facturas->count() }} facturas emitidas</span>
            </div>
        </div>
        
        <!-- Open Balance Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col justify-center shadow-sm">
            <h3 class="font-bold text-[11px] text-slate-500 uppercase tracking-wider mb-2">Saldo Pendiente</h3>
            @php
                $pendientes = $facturas->where('estado', \App\Enums\EstadoFactura::PENDIENTE);
            @endphp
            <div class="text-2xl font-bold text-red-600 mb-1">${{ number_format($pendientes->sum('total'), 2) }}</div>
            <div class="flex items-center text-slate-500 gap-1 text-sm font-medium mt-2">
                <span class="material-symbols-outlined text-[16px]">schedule</span>
                <span>{{ $pendientes->count() }} facturas pendientes</span>
            </div>
        </div>
        
        <!-- Contact Person Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col shadow-sm">
            <h3 class="font-bold text-[11px] text-slate-500 uppercase tracking-wider mb-4">Información Adicional</h3>
            <div class="flex items-center gap-4 mt-auto mb-auto">
                <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center border border-slate-200 overflow-hidden flex-shrink-0 text-slate-400">
                    <span class="material-symbols-outlined text-[24px]">domain</span>
                </div>
                <div>
                    <div class="text-base text-sovereign-blue font-bold">{{ $cliente->nombre }}</div>
                    <div class="text-sm text-slate-500">Cliente desde {{ $cliente->created_at->format('M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table Section -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-slate-200 bg-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sticky top-0 z-10">
            <h2 class="text-xl font-bold text-sovereign-blue">Historial de Facturas</h2>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('facturas', ['search' => $cliente->nombre]) }}" wire:navigate class="text-sm text-sovereign-blue hover:underline font-medium">Ver todas en Facturación</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-3 font-bold text-[11px] text-slate-500 uppercase tracking-wider">Número</th>
                        <th class="px-6 py-3 font-bold text-[11px] text-slate-500 uppercase tracking-wider">Fecha Emisión</th>
                        <th class="px-6 py-3 font-bold text-[11px] text-slate-500 uppercase tracking-wider text-right">Total</th>
                        <th class="px-6 py-3 font-bold text-[11px] text-slate-500 uppercase tracking-wider text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($facturas as $factura)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4 text-sovereign-blue font-bold text-sm">
                            <a href="{{ route('facturas.show', $factura->id) }}" wire:navigate class="hover:underline">{{ $factura->numero_factura }}</a>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-sm">
                            {{ $factura->fecha_emision->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sovereign-blue font-medium text-sm">
                            ${{ number_format($factura->total, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($factura->estado === \App\Enums\EstadoFactura::PAGADA)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Pagada</span>
                            @elseif($factura->estado === \App\Enums\EstadoFactura::ANULADA)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">Anulada</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Pendiente</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <span class="material-symbols-outlined text-3xl text-slate-400">receipt_long</span>
                                </div>
                                <h4 class="text-base font-bold text-slate-800 mb-1">Sin facturas emitidas</h4>
                                <p class="text-sm text-slate-500 max-w-xs mb-6">Este cliente aún no tiene un historial de facturación en el sistema.</p>
                                <a href="{{ route('facturas', ['cliente_id' => $cliente->id]) }}" wire:navigate class="px-4 py-2 bg-white border border-slate-300 rounded text-[11px] font-bold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition-colors shadow-sm inline-flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    Crear Primera Factura
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($facturas->count() > 10)
        <!-- Pagination Footer -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="text-sm text-slate-500">
                Mostrando las últimas 10 facturas
            </div>
            <a href="{{ route('facturas', ['search' => $cliente->nombre]) }}" wire:navigate class="px-4 py-2 bg-white border border-slate-300 rounded text-[11px] font-bold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                Ver historial completo
            </a>
        </div>
        @endif
    </div>
</div>
