<div class="max-w-7xl mx-auto w-full space-y-6">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Facturas', 'url' => route('facturas')],
            ['title' => $factura->numero_factura]
        ]" />
    @endsection

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div class="flex items-center space-x-4">
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $factura->numero_factura }}</h2>
            
            @php
                $badgeClasses = match($factura->estado->value) {
                    'Pagada' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'Anulada' => 'bg-red-100 text-red-800 border-red-200',
                    default => 'bg-amber-100 text-amber-800 border-amber-200',
                };
            @endphp
            <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $badgeClasses }}">
                {{ $factura->estado->value }}
            </span>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            @can('facturas.estado.cambiar')
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false" class="px-4 py-2 border border-slate-200 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors flex items-center shadow-sm">
                        <span class="material-symbols-outlined text-[18px] mr-2 text-slate-500">sync_alt</span>
                        Cambiar Estado
                        <span class="material-symbols-outlined text-[18px] ml-2">arrow_drop_down</span>
                    </button>
                    
                    <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-20" style="display: none;">
                        @foreach(\App\Enums\EstadoFactura::cases() as $estado)
                            @if($estado->value !== $factura->estado->value)
                                <button wire:click="cambiarEstado('{{ $estado->value }}')" @click="open = false" class="block w-full text-left px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-sovereign-blue transition-colors">
                                    Marcar como {{ $estado->value }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endcan

            <a href="{{ route('facturas.pdf', $factura->id) }}" class="px-4 py-2 border border-slate-200 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors flex items-center shadow-sm">
                <span class="material-symbols-outlined text-[18px] mr-2 text-slate-500">picture_as_pdf</span>
                Descargar PDF
            </a>
            
            <a href="{{ route('facturas.pdf', $factura->id) }}?print=true" target="_blank" class="px-5 py-2 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors shadow-sm flex items-center">
                <span class="material-symbols-outlined text-[18px] mr-2">print</span>
                Imprimir
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Detalles Principales -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
                
                <div class="flex justify-between items-start mb-8 pb-6 border-b border-slate-100">
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Facturado a:</h3>
                        <p class="text-lg font-bold text-slate-800">{{ $factura->cliente->nombre }}</p>
                        @if($factura->cliente->identificacion_fiscal)
                            <p class="text-sm font-medium text-slate-500 mt-1">NIT/ID: {{ $factura->cliente->identificacion_fiscal }}</p>
                        @endif
                    </div>
                    
                    <div class="text-right">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Detalles:</h3>
                        <p class="text-sm text-slate-700 mb-1"><span class="font-medium mr-2">Emisión:</span> {{ $factura->fecha_emision->format('d/m/Y') }}</p>
                        @if($factura->fecha_vencimiento)
                            <p class="text-sm text-slate-700"><span class="font-medium mr-2">Vencimiento:</span> {{ $factura->fecha_vencimiento->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200">
                                <th class="px-2 py-3">Descripción</th>
                                <th class="px-2 py-3 text-right">Cant.</th>
                                <th class="px-2 py-3 text-right">Precio U.</th>
                                <th class="px-2 py-3 text-right">Desc.</th>
                                <th class="px-2 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($factura->items as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors text-sm">
                                <td class="px-2 py-4 text-slate-800 font-medium">{{ $item->descripcion }}</td>
                                <td class="px-2 py-4 text-right text-slate-600">{{ $item->cantidad }}</td>
                                <td class="px-2 py-4 text-right text-slate-600">${{ number_format($item->precio_unitario, 2) }}</td>
                                <td class="px-2 py-4 text-right text-slate-600">
                                    {{ $item->descuento_porcentaje > 0 ? $item->descuento_porcentaje . '%' : '-' }}
                                </td>
                                <td class="px-2 py-4 text-right font-semibold text-slate-800">${{ number_format($item->subtotal_linea, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex flex-col items-end border-t border-slate-100 pt-6">
                    <div class="w-full sm:w-2/3 md:w-1/2 space-y-4">
                        <div class="flex justify-between items-center text-sm text-slate-600">
                            <span class="font-medium">Subtotal:</span>
                            <span class="font-semibold text-slate-800 text-base">${{ number_format($factura->subtotal, 2) }}</span>
                        </div>
                        
                        @if($factura->descuento_total > 0)
                        <div class="flex justify-between items-center text-sm text-slate-600">
                            <span class="font-medium">Descuento ({{ $factura->descuento_porcentaje }}%):</span>
                            <span class="font-semibold text-red-600 text-base">-${{ number_format($factura->descuento_total, 2) }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between items-center text-sm text-slate-600 pb-4 border-b border-slate-100">
                            <span class="font-medium">Impuesto:</span>
                            <span class="font-semibold text-slate-800 text-base">${{ number_format($factura->impuesto, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center text-xl font-bold text-slate-900 pt-2">
                            <span>Total:</span>
                            <span class="text-sovereign-blue">${{ number_format($factura->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if($factura->notas)
                <div class="mt-8 bg-slate-50 rounded-lg p-4 border border-slate-100">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                        <span class="material-symbols-outlined text-[16px] mr-1">notes</span>
                        Notas:
                    </h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $factura->notas }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Panel Lateral Informativo -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center">
                        <span class="material-symbols-outlined text-sovereign-blue mr-2 text-[20px]">info</span>
                        Información Adicional
                    </h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <!-- Vendedor Asignado -->
                    <div class="flex items-start bg-blue-50/50 p-4 rounded-lg border border-blue-100/50 transition-colors hover:bg-blue-50">
                        <div class="flex-shrink-0 bg-blue-100/80 rounded-full p-2 mr-4 shadow-sm border border-blue-200/50">
                            <span class="material-symbols-outlined text-sovereign-blue text-[20px] block">badge</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Vendedor Asignado</span>
                            <div class="text-sm font-bold text-slate-800">
                                {{ $factura->vendedor->user->name }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fecha Creación -->
                    <div class="flex items-start bg-slate-50 p-4 rounded-lg border border-slate-100 transition-colors hover:bg-slate-100/50">
                        <div class="flex-shrink-0 bg-white rounded-full p-2 mr-4 shadow-sm border border-slate-200/60">
                            <span class="material-symbols-outlined text-slate-600 text-[20px] block">calendar_today</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Creado el</span>
                            <div class="text-sm font-bold text-slate-800">
                                {{ $factura->created_at->format('d/m/Y') }}
                                <span class="text-slate-500 font-medium ml-1">{{ $factura->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
