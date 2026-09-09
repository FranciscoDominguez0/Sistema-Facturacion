<div class="w-full space-y-6 pb-12" x-data="{ productosCatalogo: {{ Js::from($productos->map(fn($p) => ['id' => $p->id, 'nombre' => $p->nombre])->values()) }} }">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Facturas', 'url' => route('facturas')],
            ['title' => 'Nueva Venta']
        ]" />
    @endsection

    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Nueva Venta</h2>
        <p class="text-slate-500 text-sm mt-1">Registra una nueva factura de venta en el sistema.</p>
    </div>

    <!-- Layout Principal en 1 columna -->
    <div class="space-y-6">
            
            <!-- Top Section: Cliente, Fechas, y Extras -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
                <!-- Box Cliente -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <label class="block text-sm font-semibold text-slate-700 mb-3">Cliente <span class="text-red-500">*</span></label>

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        @if($cliente_id)
                            <!-- Seleccionado -->
                            <div class="flex items-center justify-between bg-slate-50 border border-slate-200 px-4 py-3 rounded-lg">
                                <div class="flex items-center overflow-hidden">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400 mr-2 flex-shrink-0">person</span>
                                    <span class="text-sm font-medium text-slate-800 truncate">{{ $cliente_seleccionado_nombre }}</span>
                                </div>
                                <button wire:click="deseleccionarCliente" type="button" class="text-slate-400 hover:text-red-500 transition-colors ml-2 flex-shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        @else
                            <!-- Trigger -->
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-white border border-slate-200 hover:border-slate-300 rounded-lg px-4 py-3 text-sm text-slate-400 focus:outline-none focus:ring-1 focus:ring-sovereign-blue focus:border-sovereign-blue transition-colors">
                                <span x-show="!open">Seleccione un cliente...</span>
                                <span x-show="open" class="text-slate-700">Buscar cliente...</span>
                                <span class="material-symbols-outlined text-slate-400 text-[20px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="absolute z-30 w-full mt-1 bg-white rounded-lg border border-slate-200 shadow-lg overflow-hidden" style="display:none">
                                <!-- Search inside dropdown -->
                                <div class="p-2 border-b border-slate-100">
                                    <input type="text" wire:model.live.debounce.300ms="searchCliente" class="w-full bg-slate-50 border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-md px-3 py-2 text-sm text-slate-800 focus:outline-none placeholder-slate-400" placeholder="Buscar..." @click.stop x-ref="clienteInput" x-init="$watch('open', v => v && $nextTick(() => $refs.clienteInput.focus()))">
                                </div>
                                <!-- Results -->
                                <ul class="max-h-48 overflow-y-auto py-1">
                                    @if(count($clientes_sugeridos) > 0)
                                        @foreach($clientes_sugeridos as $sugerencia)
                                            <li>
                                                <button type="button" wire:click="seleccionarCliente({{ $sugerencia['id'] }}, '{{ addslashes($sugerencia['nombre']) }}'); open = false" @click="open = false" class="w-full text-left px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                                    {{ $sugerencia['nombre'] }}
                                                </button>
                                            </li>
                                        @endforeach
                                    @elseif(!empty($searchCliente))
                                        <li class="px-4 py-3 text-sm text-slate-400">Sin resultados.</li>
                                    @else
                                        <li class="px-4 py-3 text-sm text-slate-400 italic">Escribe para buscar...</li>
                                    @endif
                                </ul>
                                <!-- Crear nuevo -->
                                <div class="border-t border-slate-100">
                                    <button type="button" @click="$wire.set('mostrarModalCliente', true); open = false" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-sovereign-blue hover:bg-blue-50 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">add</span>
                                        Nuevo Cliente
                                    </button>
                                </div>
                            </div>
                        @endif
                        <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                    </div>
                </div>

                <!-- Box Fechas y Vendedor -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="space-y-5">
                        <!-- Fecha Factura -->
                        <div class="flex items-center justify-between gap-4">
                            <label class="text-sm font-semibold text-slate-600 leading-tight">Fecha de Factura <span class="text-red-500">*</span></label>
                            <div class="w-48 sm:w-56 lg:w-64">
                                <input type="date" wire:model="form.fecha_emision" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                                <x-input-error :messages="$errors->get('fecha_emision')" class="mt-1 text-xs" />
                            </div>
                        </div>
                        
                        <!-- Fecha Pago -->
                        <div class="flex items-center justify-between gap-4">
                            <label class="text-sm font-semibold text-slate-600 leading-tight">Fecha de Pago</label>
                            <div class="w-48 sm:w-56 lg:w-64">
                                <input type="date" wire:model="form.fecha_vencimiento" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                                <x-input-error :messages="$errors->get('fecha_vencimiento')" class="mt-1 text-xs" />
                            </div>
                        </div>

                        <!-- Vendedor -->
                        <div class="flex items-center justify-between gap-4">
                            <label class="text-sm font-semibold text-slate-600 leading-tight">Vendedor <span class="text-red-500">*</span></label>
                            <div class="w-48 sm:w-56 lg:w-64">
                                @if(Gate::allows('facturas.vendedor.seleccionar'))
                                    @if($form->vendedor_id)
                                        <!-- Seleccionado -->
                                        <div class="flex items-center justify-between bg-slate-50 border border-slate-200 px-3 py-2.5 rounded-lg">
                                            <div class="flex items-center overflow-hidden">
                                                <span class="material-symbols-outlined text-[16px] text-slate-400 mr-2 flex-shrink-0">person</span>
                                                <span class="text-sm font-medium text-slate-800 truncate">{{ $vendedor_seleccionado_nombre }}</span>
                                            </div>
                                            <button wire:click="deseleccionarVendedor" type="button" class="text-slate-400 hover:text-red-500 transition-colors ml-2 flex-shrink-0">
                                                <span class="material-symbols-outlined text-[16px]">close</span>
                                            </button>
                                        </div>
                                    @else
                                        <!-- Dropdown vendedor -->
                                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-white border border-slate-200 hover:border-slate-300 rounded-lg px-3 py-2.5 text-sm text-slate-400 focus:outline-none focus:ring-1 focus:ring-sovereign-blue focus:border-sovereign-blue transition-colors">
                                                <span x-show="!open">Seleccione...</span>
                                                <span x-show="open" class="text-slate-700">Buscar...</span>
                                                <span class="material-symbols-outlined text-slate-400 text-[18px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                                            </button>

                                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="absolute z-30 w-full mt-1 bg-white rounded-lg border border-slate-200 shadow-lg overflow-hidden" style="display:none">
                                                <div class="p-2 border-b border-slate-100">
                                                    <input type="text" wire:model.live.debounce.300ms="searchVendedor" class="w-full bg-slate-50 border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-md px-3 py-2 text-sm text-slate-800 focus:outline-none placeholder-slate-400" placeholder="Buscar..." @click.stop x-ref="vendedorInput" x-init="$watch('open', v => v && $nextTick(() => $refs.vendedorInput.focus()))">
                                                </div>
                                                <ul class="max-h-40 overflow-y-auto py-1">
                                                    @if(count($vendedores_sugeridos) > 0)
                                                        @foreach($vendedores_sugeridos as $sugerencia)
                                                            <li>
                                                                <button type="button" wire:click="seleccionarVendedor({{ $sugerencia['id'] }}, '{{ addslashes($sugerencia['name']) }}')" @click="open = false" class="w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                                                    {{ $sugerencia['name'] }}
                                                                </button>
                                                            </li>
                                                        @endforeach
                                                    @elseif(!empty($searchVendedor))
                                                        <li class="px-3 py-2 text-sm text-slate-400">Sin resultados.</li>
                                                    @else
                                                        <li class="px-3 py-2 text-sm text-slate-400 italic">Escribe para buscar...</li>
                                                    @endif
                                                </ul>
                                                <div class="border-t border-slate-100">
                                                    <button type="button" @click="$wire.set('mostrarModalVendedor', true); open = false" class="w-full flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-sovereign-blue hover:bg-blue-50 transition-colors">
                                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                                        Nuevo Vendedor
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <x-input-error :messages="$errors->get('form.vendedor_id')" class="mt-1 text-xs" />
                                @else
                                    <div class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-500 cursor-not-allowed truncate">
                                        {{ Auth::user()->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Box Factura y Descuento -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="space-y-5">
                        <!-- Factura # -->
                        <div class="flex items-center justify-between gap-4">
                            <label class="text-sm font-semibold text-slate-600 leading-tight">Factura #</label>
                            <div class="w-48 sm:w-56 lg:w-64">
                                <div class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 shadow-sm tracking-wide">
                                    {{ $numero_factura_preview }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Box Líneas -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-medium text-slate-800 flex items-center">
                        <span class="material-symbols-outlined mr-2 text-slate-400">list_alt</span>
                        Líneas de Venta
                    </h3>
                </div>
                <div class="p-4">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200">
                                <th class="px-2 py-3">Producto / Descripción</th>
                                <th class="px-2 py-3 w-24 text-center">Cant.</th>
                                <th class="px-2 py-3 w-32 text-right">Precio U.</th>
                                <th class="px-2 py-3 w-24 text-center">Desc %</th>
                                <th class="px-2 py-3 w-28 text-right">Subtotal</th>
                                <th class="px-2 py-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($form->items as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-2 py-3 align-top">
                                    <div class="space-y-3">
                                        <!-- Selector de Producto (Autocomplete) -->
                                        @if($item['producto_id'])
                                            <div class="flex items-center justify-between w-full bg-white border border-slate-200 rounded-md px-3 py-1.5 text-sm text-slate-800 shadow-sm">
                                                <span class="truncate font-medium text-slate-700">{{ collect($productos)->firstWhere('id', $item['producto_id'])->nombre ?? 'Producto seleccionado' }}</span>
                                                <button type="button" wire:click="seleccionarProducto({{ $index }}, null)" class="text-slate-400 hover:text-red-500 transition-colors p-0.5 rounded hover:bg-slate-100" title="Cambiar producto">
                                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                                </button>
                                            </div>
                                        @else
                                            <div x-data="{
                                                open: false,
                                                search: '',
                                                get filtered() {
                                                    if (this.search === '') return productosCatalogo;
                                                    return productosCatalogo.filter(p => p.nombre.toLowerCase().includes(this.search.toLowerCase()));
                                                }
                                            }" @click.away="open = false" class="relative">
                                                <div class="relative">
                                                    <input type="text" 
                                                           x-model="search" 
                                                           @focus="open = true" 
                                                           placeholder="Buscar producto..."
                                                           class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-md px-3 py-1.5 pr-8 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors"
                                                           autocomplete="off">
                                                    <button type="button" x-show="search.length > 0" @click="search = ''" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5 rounded-full hover:bg-slate-100 transition-colors">
                                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                                    </button>
                                                </div>

                                                <div x-show="open" class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-md shadow-lg">
                                                    <ul class="max-h-48 overflow-auto py-1">
                                                        <template x-for="prod in filtered" :key="prod.id">
                                                            <li>
                                                                <button type="button" @click="$wire.seleccionarProducto({{ $index }}, prod.id); open = false;"
                                                                        class="w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                                                    <span x-text="prod.nombre"></span>
                                                                </button>
                                                            </li>
                                                        </template>
                                                        <template x-if="filtered.length === 0">
                                                            <li class="px-3 py-2 text-sm text-slate-500 text-center">No hay coincidencias</li>
                                                        </template>
                                                        <li class="border-t border-slate-100 mt-1">
                                                            <button type="button" @click="$wire.seleccionarProducto({{ $index }}, 'nuevo_producto'); open = false;"
                                                                    class="w-full text-left px-3 py-2 text-sm font-medium text-sovereign-blue hover:bg-slate-50 flex items-center transition-colors">
                                                                <span class="material-symbols-outlined text-[18px] mr-1.5">add</span>
                                                                Nuevo Producto
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Descripción editable (aparece debajo) -->
                                        <input type="text" wire:model.live.debounce.500ms="form.items.{{ $index }}.descripcion" placeholder="Descripción detallada..." class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-md px-3 py-1.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors mt-2">
                                        <x-input-error :messages="$errors->get('items.'.$index.'.descripcion')" class="mt-1 text-xs" />
                                    </div>
                                </td>
                                <td class="px-2 py-3 align-top pt-3">
                                    <input type="number" step="0.01" min="0.01" wire:model.live.debounce.500ms="form.items.{{ $index }}.cantidad" class="w-full text-center bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-md px-2 py-1.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                                </td>
                                <td class="px-2 py-3 align-top pt-3">
                                    <input type="number" step="0.01" min="0" wire:model.live.debounce.500ms="form.items.{{ $index }}.precio_unitario" class="w-full text-right bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-md px-3 py-1.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                                </td>
                                <td class="px-2 py-3 align-top pt-3">
                                    <input type="number" step="0.01" min="0" max="100" wire:model.live.debounce.500ms="form.items.{{ $index }}.descuento_porcentaje" class="w-full text-center bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-md px-2 py-1.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                                </td>
                                <td class="px-2 py-3 align-top pt-3 text-right">
                                    <span class="inline-block mt-1.5 font-medium text-slate-800 text-sm">
                                        ${{ number_format($item['subtotal_linea'], 2) }}
                                    </span>
                                </td>
                                <td class="px-2 py-3 align-top pt-3 text-center">
                                    <button type="button" wire:click="eliminarLinea({{ $index }})" class="mt-1 text-slate-400 hover:text-red-500 transition-colors p-1 rounded hover:bg-red-50" title="Eliminar línea">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @if(count($form->items) === 0)
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500 text-sm">
                                    No hay líneas en esta factura.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <x-input-error :messages="$errors->get('items')" class="mt-2 px-2 pb-2" />
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30 flex justify-center">
                    <button type="button" wire:click="agregarLinea" class="text-sm font-medium text-sovereign-blue hover:text-slate-800 flex items-center transition-colors px-4 py-2 rounded-lg hover:bg-slate-200/50">
                        <span class="material-symbols-outlined text-[18px] mr-1">add</span>
                        Añadir Línea
                    </button>
                </div>
            </div>

        <!-- Notas + Resumen al final, lado a lado -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Notas -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Notas (opcional)</label>
                <textarea wire:model="form.notas" rows="5" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors resize-none" placeholder="Términos y condiciones, instrucciones de pago..."></textarea>
            </div>

            <!-- Resumen -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <h3 class="text-base font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Resumen</h3>

                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm text-slate-600">
                        <span>Subtotal</span>
                        <span class="text-slate-900 font-medium">${{ number_format($form->subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center text-sm text-slate-600">
                        <span>Descuento global (%)</span>
                        <div class="w-24">
                            <input type="number" step="0.01" min="0" max="100" wire:model.live.debounce.500ms="form.descuento_porcentaje" class="w-full text-right bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-md px-2 py-1 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                        </div>
                    </div>

                    @if($form->descuento_total > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Ahorro</span>
                            <span class="text-red-600 font-medium">-${{ number_format($form->descuento_total, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center text-sm text-slate-600">
                        <span class="flex items-center gap-1">
                            Impuesto
                            <span class="material-symbols-outlined text-[14px] text-slate-400 cursor-help" title="Configurado en empresa">info</span>
                        </span>
                        <span class="text-slate-900 font-medium">${{ number_format($form->impuesto, 2) }}</span>
                    </div>

                    <div class="border-t border-slate-100 pt-3 mt-2 flex justify-between items-end">
                        <span class="text-base font-bold text-slate-900">Total</span>
                        <span class="text-2xl font-bold text-sovereign-blue tracking-tight">${{ number_format($form->total, 2) }}</span>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <button wire:click="save" class="w-full flex justify-center items-center py-2.5 px-4 bg-sovereign-blue hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[18px] mr-2">save</span>
                        <span wire:loading wire:target="save" class="material-symbols-outlined text-[18px] mr-2 animate-spin">progress_activity</span>
                        Guardar Venta
                    </button>
                    <a href="{{ route('facturas') }}" class="w-full flex justify-center items-center py-2.5 px-4 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Nuevo Cliente -->
    <div x-data="{ show: @entangle('mostrarModalCliente') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            
            <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" @click="show = false"></div>

            <div x-show="show" x-transition class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-slate-100">
                <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-bold text-slate-800">
                        Nuevo Cliente
                    </h3>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-500 rounded-lg p-1 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
                
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre / Razón Social <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nuevo_cliente_nombre" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                        <x-input-error :messages="$errors->get('nuevo_cliente_nombre')" class="mt-2" />
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Identificación</label>
                            <input type="text" wire:model="nuevo_cliente_identificacion" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                            <x-input-error :messages="$errors->get('nuevo_cliente_identificacion')" class="mt-1 text-xs" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Teléfono</label>
                            <input type="text" wire:model="nuevo_cliente_telefono" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                            <x-input-error :messages="$errors->get('nuevo_cliente_telefono')" class="mt-1 text-xs" />
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Correo Electrónico</label>
                        <input type="email" wire:model="nuevo_cliente_email" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                        <x-input-error :messages="$errors->get('nuevo_cliente_email')" class="mt-1 text-xs" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Dirección</label>
                        <textarea wire:model="nuevo_cliente_direccion" rows="2" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors resize-none"></textarea>
                        <x-input-error :messages="$errors->get('nuevo_cliente_direccion')" class="mt-1 text-xs" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-4 pt-4 border-t border-slate-50">
                    <button type="button" @click="show = false" class="px-5 py-2.5 bg-white border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                        Cancelar
                    </button>
                    <button type="button" wire:click="guardarClienteExpress" class="px-5 py-2.5 bg-sovereign-blue text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors shadow-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="guardarClienteExpress">Guardar Cliente</span>
                        <span wire:loading wire:target="guardarClienteExpress">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Producto -->
    <div x-data="{ show: @entangle('mostrarModalProducto') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            
            <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" @click="show = false"></div>

            <div x-show="show" x-transition class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-slate-100">
                <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-bold text-slate-800">
                        Nuevo Producto
                    </h3>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-500 rounded-lg p-1 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
                
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre / Descripción <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nuevo_producto_nombre" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                        <x-input-error :messages="$errors->get('nuevo_producto_nombre')" class="mt-2 text-xs" />
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Precio Unitario <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" wire:model="nuevo_producto_precio" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                            <x-input-error :messages="$errors->get('nuevo_producto_precio')" class="mt-1 text-xs" />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                            <select wire:model="nuevo_producto_tipo" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                                <option value="bien">Bien / Producto</option>
                                <option value="servicio">Servicio</option>
                            </select>
                            <x-input-error :messages="$errors->get('nuevo_producto_tipo')" class="mt-1 text-xs" />
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center space-x-2 mt-4 cursor-pointer">
                            <input type="checkbox" wire:model="nuevo_producto_aplica_impuesto" class="rounded border-slate-300 text-sovereign-blue shadow-sm focus:ring-sovereign-blue">
                            <span class="text-sm font-medium text-slate-700">Aplica Impuesto</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-4 pt-4 border-t border-slate-50">
                    <button type="button" @click="show = false" class="px-5 py-2.5 bg-white border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                        Cancelar
                    </button>
                    <button type="button" wire:click="guardarProductoExpress" class="px-5 py-2.5 bg-sovereign-blue text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors shadow-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="guardarProductoExpress">Guardar Producto</span>
                        <span wire:loading wire:target="guardarProductoExpress">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Vendedor -->
    <x-modal-action show="mostrarModalVendedor" title="Nuevo Vendedor" maxWidth="sm">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                <input type="text" wire:model="nuevo_vendedor_nombre" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors" @keydown.enter="$wire.guardarVendedorExpress()">
                <x-input-error :messages="$errors->get('nuevo_vendedor_nombre')" class="mt-1 text-xs" />
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Correo Electrónico (Opcional)</label>
                <input type="email" wire:model="nuevo_vendedor_email" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors" @keydown.enter="$wire.guardarVendedorExpress()">
                <x-input-error :messages="$errors->get('nuevo_vendedor_email')" class="mt-1 text-xs" />
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Contraseña (Opcional)</label>
                <input type="password" wire:model="nuevo_vendedor_password" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors" @keydown.enter="$wire.guardarVendedorExpress()">
                <x-input-error :messages="$errors->get('nuevo_vendedor_password')" class="mt-1 text-xs" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-50">
            <button type="button" @click="mostrarModalVendedor = false" class="px-4 py-2 bg-white border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                Cancelar
            </button>
            <button type="button" wire:click="guardarVendedorExpress" class="px-4 py-2 bg-sovereign-blue text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors shadow-sm" wire:loading.attr="disabled" wire:target="guardarVendedorExpress">
                <span wire:loading.remove wire:target="guardarVendedorExpress">Guardar</span>
                <span wire:loading wire:target="guardarVendedorExpress">Guardando...</span>
            </button>
        </div>
    </x-modal-action>
</div>
