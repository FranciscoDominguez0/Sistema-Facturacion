{{-- Listado reutilizable del área de configuración (usuarios, impuestos, etc.):
     tarjeta con título, acciones masivas, filtro, buscador, botón de creación,
     tabla con selección y paginación.
     Props: titulo, metodoCrear (método Livewire del botón), textoCrear,
     seleccionados, idsPagina, paginador (opcional).
     Slots: filtros (controles extra), cabeceras (<th>), filas (<tr> del tbody).
     Uso:
     <x-listado titulo="Usuarios" metodo-crear="crearUsuario" texto-crear="Nuevo Usuario"
         :seleccionados="$seleccionados" :ids-pagina="$idsPagina" :paginador="$usuarios">
         <x-slot name="cabeceras">...</x-slot>
         <x-slot name="filas">...</x-slot>
     </x-listado> --}}
@props([
    'titulo',
    'metodoCrear',
    'textoCrear',
    'seleccionados' => [],
    'idsPagina' => [],
    'paginador' => null,
])

<div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden flex flex-col">
    <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h2 class="text-xl text-slate-900 font-bold">{{ $titulo }}</h2>

        <div class="flex items-center gap-2 w-full md:w-auto">
            @if (count($seleccionados) > 0)
                <x-dropdown align="left" width="48">
                    <x-slot name="trigger">
                        <div class="bg-slate-800 text-white text-sm px-4 py-2 rounded-md font-medium cursor-pointer flex items-center gap-2">
                            Comportamiento
                            <span class="material-symbols-outlined text-[16px]">expand_more</span>
                        </div>
                    </x-slot>
                    <x-slot name="content">
                        <button wire:click="confirmarEliminacionMasiva" type="button" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                            Eliminar
                        </button>
                    </x-slot>
                </x-dropdown>
            @endif

            <div class="flex-1 md:w-64">
                <input wire:model.live.debounce.300ms="search" class="w-full bg-white border border-slate-200 rounded-md px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-slate-300" placeholder="Filtrar" type="text"/>
            </div>

            {{ $filtros ?? '' }}

            <button wire:click="{{ $metodoCrear }}" type="button" class="bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-slate-800 transition-colors ml-2">
                {{ $textoCrear }}
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-white">
                    <th class="py-3 px-4 w-12 text-center">
                        <input type="checkbox" class="rounded border-slate-300 text-slate-900 shadow-sm cursor-pointer" wire:click="seleccionarTodos" @checked($idsPagina && count(array_diff($idsPagina, $seleccionados)) === 0)>
                    </th>
                    {{ $cabeceras }}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                {{ $filas }}
            </tbody>
        </table>

        @if ($paginador)
            <x-paginacion :paginador="$paginador" />
        @endif
    </div>
</div>