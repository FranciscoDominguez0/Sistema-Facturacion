<div class="w-full">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Clientes']
        ]" />
    @endsection

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-2xl font-bold text-slate-800 whitespace-nowrap">Clientes</h2>
        
        <!-- Actions & Filters -->
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            <!-- Search Input -->
            <div class="relative w-full sm:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input wire:model.live.debounce.500ms="search" class="w-full bg-white border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm text-slate-800 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue transition-colors" placeholder="Buscar clientes..." type="text"/>
            </div>
            
            <!-- Filter Dropdown -->
            <div class="relative w-full sm:w-auto">
                <select wire:model.live="filtroEstado" class="w-full appearance-none bg-white bg-none border border-slate-200 rounded-lg pl-4 pr-10 py-2 text-sm text-slate-800 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue cursor-pointer transition-colors">
                    <option value="Todos">Todos</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>
            
            <!-- Main Action -->
            <a href="{{ route('clientes.create') }}" wire:navigate class="w-full sm:w-auto bg-sovereign-blue text-white px-6 py-2 rounded-lg text-sm font-medium border border-sovereign-blue hover:bg-opacity-90 transition-colors shadow-sm flex items-center justify-center gap-2 whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Nuevo Cliente
            </a>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Nombre</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Identificación</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Email</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Teléfono</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Estado</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap hidden lg:table-cell">Fecha de Creación</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right whitespace-nowrap sticky right-0 bg-slate-50 shadow-[-10px_0_15px_-3px_rgba(0,0,0,0.02)] z-10">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($clientes as $cliente)
                    <tr class="hover:bg-slate-50 transition-colors group" wire:key="{{ $cliente->id }}">
                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-800 font-semibold">{{ $cliente->nombre }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            {{ $cliente->identificacion ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 break-all min-w-[200px]">
                            {{ $cliente->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            {{ $cliente->telefono ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cliente->activo)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold tracking-wide uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold tracking-wide uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 hidden lg:table-cell">
                            {{ $cliente->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium sticky right-0 bg-white group-hover:bg-slate-50 transition-colors shadow-[-10px_0_15px_-3px_rgba(0,0,0,0.02)]">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('clientes.show', $cliente) }}" wire:navigate class="text-slate-400 hover:text-sovereign-blue p-1 rounded transition-colors" title="Ver">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </a>
                                <a href="{{ route('clientes.edit', $cliente) }}" wire:navigate class="text-slate-400 hover:text-sovereign-blue p-1 rounded transition-colors" title="Editar">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                @if($cliente->activo)
                                    <button wire:click="toggleActivo({{ $cliente->id }})" class="text-slate-400 hover:text-red-600 p-1 rounded transition-colors" title="Desactivar">
                                        <span class="material-symbols-outlined text-[20px]">block</span>
                                    </button>
                                @else
                                    <button wire:click="toggleActivo({{ $cliente->id }})" class="text-slate-400 hover:text-emerald-600 p-1 rounded transition-colors" title="Activar">
                                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <span class="material-symbols-outlined text-4xl mb-3 text-slate-300">group_off</span>
                                <p class="text-base font-medium text-slate-800 mb-1">No hay clientes registrados</p>
                                <p class="text-sm mb-4">Comienza agregando un nuevo cliente al sistema.</p>
                                <a href="{{ route('clientes.create') }}" wire:navigate class="text-sm text-sovereign-blue font-medium hover:underline">
                                    + Agregar Cliente
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Footer -->
        @if($clientes->hasPages())
        <div class="bg-slate-50 px-6 py-3 border-t border-slate-200">
            {{ $clientes->links() }}
        </div>
        @endif
    </div>
</div>
