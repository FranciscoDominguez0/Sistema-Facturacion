<div class="w-full space-y-6 pb-12">
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

                    <div>
                        <x-select-searchable 
                            wire:model="cliente_id" 
                            :options="$clientes" 
                            placeholder="Seleccione un cliente..." 
                            action-text="Nuevo Cliente" 
                            action-click="$wire.set('mostrarModalCliente', true)" 
                        />
                        <x-input-error :messages="$errors->get('cliente_id')" class="mt-2 text-xs" />
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
                                    <x-select-searchable 
                                        wire:model="form.vendedor_id" 
                                        :options="$vendedores" 
                                        placeholder="Seleccione vendedor..." 
                                        action-text="Nuevo Vendedor" 
                                        action-click="$wire.set('mostrarModalVendedor', true)" 
                                    />
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
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
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
                                <th class="px-2 py-3 w-44 text-center">Impuesto</th>
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
                                        <x-select-searchable 
                                            wire:model.live="form.items.{{ $index }}.producto_id" 
                                            :options="$productos" 
                                            placeholder="Buscar producto..." 
                                            action-text="Nuevo Producto" 
                                            action-click="$wire.set('linea_producto_actual', {{ $index }}); $wire.set('mostrarModalProducto', true);" 
                                        />
                                        
                                        <!-- Descripción editable (aparece debajo) -->
                                        <input type="text" wire:model.live.debounce.500ms="form.items.{{ $index }}.descripcion" placeholder="Descripción detallada..." class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-md px-3 py-1.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors mt-2">
                                        <x-input-error :messages="$errors->get('items.'.$index.'.descripcion')" class="mt-1 text-xs" />
                                    </div>
                                </td>
                                <td class="px-2 py-3 align-top pt-3">
                                    <input type="number" step="1" min="1" wire:model.live.debounce.500ms="form.items.{{ $index }}.cantidad" class="w-full text-center bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-md px-2 py-1.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                                </td>
                                <td class="px-2 py-3 align-top pt-3">
                                    <x-precio-input wire:model.live.debounce.500ms="form.items.{{ $index }}.precio_unitario" class="!px-3 !py-1.5 !rounded-md" />
                                </td>
                                <td class="px-2 py-3 align-top pt-3">
                                    <x-select-searchable 
                                        compact
                                        wire:model.live="form.items.{{ $index }}.impuesto_id" 
                                        :options="$opcionesImpuestos" 
                                        placeholder="Exento" 
                                        action-text="Nuevo Impuesto"
                                        action-click="$wire.set('linea_impuesto_actual', {{ $index }}); $wire.set('mostrarModalImpuesto', true);"
                                    />
                                </td>
                                <td class="px-2 py-3 align-top pt-3 text-right">
                                    <span class="inline-block mt-1.5 font-medium text-slate-800 text-sm">
                                        ${{ number_format($item['subtotal_linea'], 2) }}
                                    </span>
                                    @if(isset($item['impuesto_monto']) && $item['impuesto_monto'] > 0)
                                    <div class="text-[10px] text-slate-500 mt-0.5">
                                        +${{ number_format($item['impuesto_monto'], 2) }}
                                    </div>
                                    @else
                                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">Exento</div>
                                    @endif
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
                                <td colspan="7" class="px-6 py-8 text-center text-slate-500 text-sm">
                                    No hay líneas en esta factura.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <x-input-error :messages="$errors->get('items')" class="mt-2 px-2 pb-2" />
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30 flex justify-center rounded-b-xl">
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

                    @foreach($form->desglose_impuestos as $nombre => $monto)
                        <div class="flex justify-between text-sm text-slate-600">
                            <span>{{ $nombre }}</span>
                            <span>${{ number_format($monto, 2) }}</span>
                        </div>
                    @endforeach

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
                            <x-precio-input wire:model="nuevo_producto_precio" />
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

    <!-- Modal Nuevo Impuesto -->
    <x-modal-action show="mostrarModalImpuesto" title="Nuevo Impuesto" maxWidth="sm">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                <input type="text" wire:model="nuevo_impuesto_nombre" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors" @keydown.enter="$wire.guardarImpuestoExpress()">
                <x-input-error :messages="$errors->get('nuevo_impuesto_nombre')" class="mt-1 text-xs" />
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Porcentaje (%) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" max="100" wire:model="nuevo_impuesto_porcentaje" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors" @keydown.enter="$wire.guardarImpuestoExpress()">
                <x-input-error :messages="$errors->get('nuevo_impuesto_porcentaje')" class="mt-1 text-xs" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-50">
            <button type="button" @click="mostrarModalImpuesto = false" class="px-4 py-2 bg-white border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                Cancelar
            </button>
            <button type="button" wire:click="guardarImpuestoExpress" class="px-4 py-2 bg-sovereign-blue text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors shadow-sm" wire:loading.attr="disabled" wire:target="guardarImpuestoExpress">
                <span wire:loading.remove wire:target="guardarImpuestoExpress">Guardar</span>
                <span wire:loading wire:target="guardarImpuestoExpress">Guardando...</span>
            </button>
        </div>
    </x-modal-action>
</div>
