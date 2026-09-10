<div class="w-full">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Productos']
        ]" />
    @endsection

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-2xl font-bold text-slate-800 whitespace-nowrap">Productos</h2>
        
        <!-- Actions & Filters -->
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            <!-- Search Input -->
            <div class="relative w-full sm:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input wire:model.live.debounce.300ms="search" class="w-full bg-white border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm text-slate-800 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue transition-colors" placeholder="Buscar productos por nombre o código..." type="text"/>
            </div>
            
            <!-- Filter Dropdown Tipo -->
            <div class="relative w-full sm:w-auto">
                <select wire:model.live="filtroTipo" class="w-full appearance-none bg-white bg-none border border-slate-200 rounded-lg pl-4 pr-10 py-2 text-sm text-slate-800 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue cursor-pointer transition-colors">
                    <option value="Todos">Todos (Tipos)</option>
                    <option value="producto">Producto</option>
                    <option value="servicio">Servicio</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>

            <!-- Filter Dropdown Estado -->
            <div class="relative w-full sm:w-auto">
                <select wire:model.live="filtroEstado" class="w-full appearance-none bg-white bg-none border border-slate-200 rounded-lg pl-4 pr-10 py-2 text-sm text-slate-800 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue cursor-pointer transition-colors">
                    <option value="Todos">Todos (Estado)</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>
            
            <!-- Main Action -->
            <a href="{{ route('productos.create') }}" wire:navigate class="w-full sm:w-auto bg-sovereign-blue text-white px-6 py-2 rounded-lg text-sm font-medium border border-sovereign-blue hover:bg-opacity-90 transition-colors shadow-sm flex items-center justify-center gap-2 whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Producto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Código</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Tipo</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Precio</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Estado</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right whitespace-nowrap sticky right-0 bg-slate-50 shadow-[-10px_0_15px_-3px_rgba(0,0,0,0.02)] z-10">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($productos as $producto)
                    <tr class="hover:bg-slate-50 transition-colors group" wire:key="{{ $producto->id }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0 flex items-center justify-center">
                                    @if($producto->imagen_path)
                                        <img src="{{ Storage::url($producto->imagen_path) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="material-symbols-outlined text-slate-400">inventory_2</span>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm text-slate-800 font-semibold">{{ $producto->nombre }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-[200px]">{{ $producto->descripcion ?? 'Sin descripción' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            {{ $producto->codigo ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 capitalize">
                            {{ $producto->tipo }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-800">${{ number_format($producto->precio, 2) }}</div>
                            <div class="text-[10px] text-slate-400">
                                {{ $producto->impuesto ? $producto->impuesto->nombre.' ('.number_format($producto->impuesto->porcentaje, 2).'%)' : 'Exento' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($producto->activo)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold tracking-wide uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold tracking-wide uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium sticky right-0 bg-white group-hover:bg-slate-50 transition-colors shadow-[-10px_0_15px_-3px_rgba(0,0,0,0.02)]">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('productos.edit', $producto) }}" wire:navigate class="text-slate-400 hover:text-sovereign-blue p-1 rounded transition-colors" title="Editar">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                @if($producto->activo)
                                    <button wire:click="toggleActivo({{ $producto->id }})" class="text-slate-400 hover:text-red-600 p-1 rounded transition-colors" title="Desactivar">
                                        <span class="material-symbols-outlined text-[20px]">block</span>
                                    </button>
                                @else
                                    <button wire:click="toggleActivo({{ $producto->id }})" class="text-slate-400 hover:text-emerald-600 p-1 rounded transition-colors" title="Activar">
                                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <span class="material-symbols-outlined text-4xl mb-3 text-slate-300">inventory_2</span>
                                <p class="text-base font-medium text-slate-800 mb-1">No hay productos registrados</p>
                                <p class="text-sm mb-4">Comienza agregando un nuevo producto o servicio a tu inventario.</p>
                                <a href="{{ route('productos.create') }}" wire:navigate class="text-sm text-sovereign-blue font-medium hover:underline">
                                    + Nuevo Producto
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <x-paginacion :paginador="$productos" />
    </div>
</div>
