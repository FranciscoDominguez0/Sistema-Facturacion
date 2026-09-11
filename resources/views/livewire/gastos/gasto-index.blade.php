<div class="w-full">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Gastos']
        ]" />
    @endsection

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-2xl font-bold text-slate-800 whitespace-nowrap">Gastos</h2>

        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            <!-- Search Input -->
            <div class="relative w-full sm:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input wire:model.live.debounce.300ms="search" class="w-full bg-white border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm text-slate-800 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue transition-colors" placeholder="Buscar por concepto o categoría..." type="text"/>
            </div>

            <!-- Main Action -->
            <a href="{{ route('gastos.crear') }}" wire:navigate class="w-full sm:w-auto bg-sovereign-blue text-white px-6 py-2 rounded-lg text-sm font-medium border border-sovereign-blue hover:bg-opacity-90 transition-colors shadow-sm flex items-center justify-center gap-2 whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Nuevo Gasto
            </a>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-visible">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Estado</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Nº Gasto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Fecha</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Monto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Concepto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right whitespace-nowrap">Comportamiento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($gastos as $gasto)
                    <tr class="hover:bg-slate-50 transition-colors" wire:key="{{ $gasto->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badgeEstado = match($gasto->estado) {
                                    'Pagado' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'Anulado' => 'bg-red-100 text-red-800 border-red-200',
                                    default => 'bg-sky-100 text-sky-800 border-sky-200',
                                };
                            @endphp
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full border {{ $badgeEstado }}">
                                {{ $gasto->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('gastos.edit', $gasto) }}" wire:navigate class="font-medium text-blue-600 hover:underline">
                                GASTO #{{ str_pad((string) $gasto->id, 6, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $gasto->fecha->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800">{{ $gasto->monto_formateado }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">{{ $gasto->concepto }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div x-data="{
                                open: false,
                                posTop: 0,
                                posRight: 0,
                                abrir($el) {
                                    const rect = $el.getBoundingClientRect();
                                    const alturaMenu = 120;
                                    const espacioAbajo = window.innerHeight - rect.bottom;
                                    this.posTop = espacioAbajo < alturaMenu
                                        ? rect.top - alturaMenu - 4
                                        : rect.bottom + 4;
                                    this.posRight = window.innerWidth - rect.right;
                                    this.open = true;
                                }
                            }" class="inline-block">
                                <button @click="open ? open = false : abrir($el)" type="button"
                                    class="bg-slate-800 text-white text-sm px-4 py-2 rounded-md font-medium cursor-pointer flex items-center gap-2 hover:bg-slate-700 transition-colors shadow-sm">
                                    Comportamiento
                                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                </button>

                                <template x-teleport="body">
                                    <div x-show="open" x-transition
                                        @click.outside="open = false"
                                        :style="`position:fixed; top:${posTop}px; right:${posRight}px; z-index:9999;`"
                                        class="w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1 text-left"
                                        style="display:none;">
                                        @can('gastos.editar')
                                            <a href="{{ route('gastos.edit', $gasto) }}" wire:navigate
                                                class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-sovereign-blue transition-colors">
                                                <span class="material-symbols-outlined text-[18px] text-slate-400">edit</span>
                                                Editar
                                            </a>
                                        @endcan

                                        @can('gastos.eliminar')
                                            <div class="border-t border-slate-100 my-1"></div>
                                            <button type="button" wire:click="confirmarEliminacion({{ $gasto->id }})" @click="open = false"
                                                class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                                Eliminar
                                            </button>
                                        @endcan
                                    </div>
                                </template>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <span class="material-symbols-outlined text-4xl mb-3 text-slate-300">receipt</span>
                                <p class="text-base font-medium text-slate-800 mb-1">No hay gastos registrados</p>
                                <p class="text-sm mb-4">Comienza registrando un nuevo gasto.</p>
                                <a href="{{ route('gastos.crear') }}" wire:navigate class="text-sm text-sovereign-blue font-medium hover:underline">
                                    + Nuevo Gasto
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <x-paginacion :paginador="$gastos" />
    </div>

    <!-- Modal Eliminar Gasto -->
    <x-modal-danger show="modalEliminarVisible" title="Eliminar Gasto" maxWidth="sm">
        @if($gastoAEliminar)
            <p class="text-sm text-slate-600">
                ¿Estás seguro de que deseas eliminar el gasto <strong>GASTO #{{ str_pad((string) $gastoAEliminar->id, 6, '0', STR_PAD_LEFT) }}</strong>? Esta acción es irreversible.
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