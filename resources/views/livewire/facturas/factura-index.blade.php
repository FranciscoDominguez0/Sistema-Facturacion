<div>
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Facturas']
        ]" />
    @endsection

    <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Listado de Facturas</h2>

        <div class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-slate-400 text-lg">search</span>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" class="pl-10 block w-full rounded-lg border-slate-300 shadow-sm focus:border-sovereign-blue focus:ring-sovereign-blue sm:text-sm text-slate-900 placeholder-slate-400" placeholder="Buscar por cliente o nº factura...">
            </div>

            <div class="flex items-center gap-2">
                <span class="text-sm text-slate-500 font-medium">Estado:</span>
                <select wire:model.live="filtroEstado" class="block w-full sm:w-40 rounded-lg border-slate-300 shadow-sm focus:border-sovereign-blue focus:ring-sovereign-blue sm:text-sm text-slate-900">
                    <option value="Todos">Todos</option>
                    @foreach(\App\Enums\EstadoFactura::cases() as $estado)
                        <option value="{{ $estado->value }}">{{ $estado->value }}</option>
                    @endforeach
                </select>
            </div>

            @can('facturas.crear')
                <a href="{{ route('facturas.crear') }}" class="inline-flex items-center justify-center px-4 py-2 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors shadow-sm" wire:navigate>
                    <span class="material-symbols-outlined text-[20px] mr-2">add</span>
                    Nueva Venta
                </a>
            @endcan
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-visible">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                        <th class="px-6 py-4">Nº Factura</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Emisión</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-right">Comportamiento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($facturas as $factura)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('facturas.edit', $factura->id) }}" wire:navigate class="font-medium text-blue-600 hover:underline">
                                {{ $factura->numero_factura }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('clientes.show', $factura->cliente_id) }}" wire:navigate class="block group">
                                <div class="text-sm text-blue-600 font-medium group-hover:underline">{{ $factura->cliente->nombre }}</div>
                            </a>
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
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click="open = !open" @click.outside="open = false" type="button"
                                    class="bg-slate-800 text-white text-sm px-4 py-2 rounded-md font-medium cursor-pointer flex items-center gap-2 hover:bg-slate-700 transition-colors shadow-sm">
                                    Comportamiento
                                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                </button>

                                <div x-show="open" x-transition class="absolute bottom-full right-0 mb-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50 text-left" style="display: none;">
                                    <a href="{{ route('facturas.edit', $factura->id) }}" wire:navigate
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-sovereign-blue transition-colors">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">edit</span>
                                        Editar
                                    </a>

                                    <button type="button" wire:click="enviarPorCorreo({{ $factura->id }})" @click="open = false"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-sovereign-blue transition-colors">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">mail</span>
                                        Enviar factura por correo
                                    </button>

                                    <a href="{{ route('facturas.pdf.vista', $factura->id) }}" wire:navigate
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-sovereign-blue transition-colors">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">visibility</span>
                                        Ver PDF
                                    </a>

                                    <button type="button" wire:click="abrirImpresion({{ $factura->id }})" @click="open = false"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-sovereign-blue transition-colors">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">print</span>
                                        Imprimir PDF
                                    </button>

                                    <a href="{{ route('facturas.pdf', $factura->id) }}"
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-sovereign-blue transition-colors">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">download</span>
                                        Descargar
                                    </a>

                                    @can('facturas.eliminar')
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <button type="button" wire:click="confirmarEliminacion({{ $factura->id }})" @click="open = false"
                                            class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                            Eliminar
                                        </button>
                                    @endcan
                                </div>
                            </div>
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
        <!-- Paginación -->
        <x-paginacion :paginador="$facturas" />
    </div>

    <!-- Iframe oculto de impresión: carga el PDF y abre el diálogo de impresión directo -->
    <iframe x-ref="pdfImpresion"
        src="{{ $facturaPdfVista ? route('facturas.pdf', $facturaPdfVista->id).'?print=true&v='.$impresionToken : 'about:blank' }}"
        @load="if ({{ $facturaPdfVista ? 'true' : 'false' }}) { setTimeout(() => $refs.pdfImpresion.contentWindow.print(), 400); }"
        class="sr-only" aria-hidden="true" title="Impresión PDF"></iframe>

    <!-- Modal Eliminar Factura -->
    <x-modal-danger show="modalEliminarVisible" title="Eliminar Factura" maxWidth="sm">
        @if($facturaAEliminar)
            <p class="text-sm text-slate-600">
                ¿Estás seguro de que deseas eliminar la factura <strong>{{ $facturaAEliminar->numero_factura }}</strong>? Esta acción es irreversible y también eliminará sus líneas de detalle.
            </p>
        @endif

        <div class="mt-6 flex justify-end gap-4 pt-4 border-t border-slate-50">
            <button type="button" @click="show = false" class="px-5 py-2.5 bg-white border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                Cancelar
            </button>
            <button type="button" wire:click="eliminar" class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-sm" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="eliminar">Eliminar</span>
                <span wire:loading wire:target="eliminar">Eliminando...</span>
            </button>
        </div>
    </x-modal-danger>
</div>