<div class="w-full">
    <x-breadcrumbs :links="[
        ['title' => 'Clientes', 'url' => route('clientes')],
        ['title' => $cliente->nombre]
    ]" />

    <!-- Client Profile Header -->
    <div class="bg-white border border-slate-200 rounded-xl p-8 mb-6 shadow-sm relative overflow-hidden">
        <!-- Abstract Decoration -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -z-0 opacity-50"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row gap-8 justify-between items-start">
            <!-- Info -->
            <div class="flex items-start gap-6">
                <!-- Avatar Placeholder -->
                <div class="w-20 h-20 rounded-xl bg-sovereign-blue/10 flex items-center justify-center text-sovereign-blue flex-shrink-0">
                    <span class="material-symbols-outlined text-[40px]">apartment</span>
                </div>
                
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-bold text-slate-800">{{ $cliente->nombre }}</h1>
                        @if($cliente->activo)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold tracking-wide uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Activo
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold tracking-wide uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                Inactivo
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-500">
                        @if($cliente->identificacion)
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px]">badge</span>
                                <span>ID: {{ $cliente->identificacion }}</span>
                            </div>
                        @endif
                        @if($cliente->email)
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px]">mail</span>
                                <a href="mailto:{{ $cliente->email }}" class="hover:text-sovereign-blue transition-colors">{{ $cliente->email }}</a>
                            </div>
                        @endif
                        @if($cliente->telefono)
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px]">call</span>
                                <a href="tel:{{ $cliente->telefono }}" class="hover:text-sovereign-blue transition-colors">{{ $cliente->telefono }}</a>
                            </div>
                        @endif
                    </div>

                    @if($cliente->direccion)
                        <div class="mt-4 flex items-start gap-1.5 text-sm text-slate-500 max-w-xl">
                            <span class="material-symbols-outlined text-[18px] mt-0.5 flex-shrink-0">location_on</span>
                            <span>{{ $cliente->direccion }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 w-full md:w-auto">
                <a href="{{ route('clientes.edit', $cliente) }}" wire:navigate class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Editar Cliente
                </a>
                <a href="{{ route('facturas', ['cliente_id' => $cliente->id]) }}" wire:navigate class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-sovereign-blue text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition-colors shadow-sm border border-sovereign-blue">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Nueva Factura
                </a>
            </div>
        </div>
    </div>

    <!-- Related Data -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Key Metrics / Stats (Left Column) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-xs font-bold tracking-wider text-slate-500 uppercase mb-4">Resumen Financiero</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Total Facturado</span>
                        <span class="font-bold text-slate-800">${{ number_format($facturas->sum('total'), 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                        <span class="text-sm text-slate-500">Facturas Emitidas</span>
                        <span class="font-bold text-slate-800">{{ $facturas->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Cliente Desde</span>
                        <span class="font-bold text-slate-800">{{ $cliente->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices List (Right Column) -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col h-full">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Historial de Facturas</h3>
                    <a href="{{ route('facturas', ['search' => $cliente->nombre]) }}" wire:navigate class="text-sm text-sovereign-blue hover:underline font-medium">Ver todas</a>
                </div>
                
                <div class="flex-1">
                    @if($facturas->count() > 0)
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Nº Factura</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Fecha</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap text-right">Monto</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($facturas->take(5) as $factura)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="#" class="text-sm font-semibold text-sovereign-blue hover:underline">{{ $factura->numero }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        {{ $factura->fecha_emision->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-slate-800">
                                        ${{ number_format($factura->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($factura->estado === 'PAGADA')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-emerald-100 text-emerald-800">Pagada</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-amber-100 text-amber-800">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <!-- Empty State -->
                        <div class="flex flex-col items-center justify-center p-12 text-center h-full">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <span class="material-symbols-outlined text-3xl text-slate-400">receipt_long</span>
                            </div>
                            <h4 class="text-base font-bold text-slate-800 mb-1">Sin facturas emitidas</h4>
                            <p class="text-sm text-slate-500 max-w-xs mb-6">Este cliente aún no tiene un historial de facturación en el sistema.</p>
                            <a href="{{ route('facturas', ['cliente_id' => $cliente->id]) }}" wire:navigate class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors shadow-sm inline-flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                                Crear Primera Factura
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
