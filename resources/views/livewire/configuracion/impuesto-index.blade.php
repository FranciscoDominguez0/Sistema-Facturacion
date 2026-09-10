<div class="space-y-6">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Configuración', 'url' => '#'],
            ['title' => 'Impuestos', 'url' => route('configuracion.impuestos')]
        ]" />
    @endsection
    
<x-settings-layout activa="impuestos">

    @if($view === 'list')
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gestión de Impuestos</h1>
                <p class="text-sm text-slate-500 mt-1">Configura los impuestos disponibles para productos y facturas.</p>
            </div>
            <button wire:click="create" class="inline-flex items-center gap-2 bg-sovereign-blue text-white font-semibold text-sm px-4 py-2.5 rounded-xl hover:bg-slate-800 shadow-sm transition-all">
                <span class="material-symbols-outlined text-sm">add</span>
                Nuevo Impuesto
            </button>
        </div>

        <div class="bg-white rounded-card border border-slate-200/80 shadow-subtle overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h2 class="text-xl text-slate-900 font-bold">Impuestos</h2>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    @if(count($seleccionados) > 0)
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
                        <input type="text" wire:model.live="search" placeholder="Buscar impuesto por nombre..." class="w-full bg-white border border-slate-200 rounded-md px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-slate-300">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">
                                <input type="checkbox" class="rounded border-slate-300 text-slate-900 shadow-sm cursor-pointer" wire:click="seleccionarTodos" @checked($this->idsPagina && count(array_diff($this->idsPagina, $seleccionados)) === 0)>
                            </th>
                            <th class="py-3 px-6">Nombre</th>
                            <th class="py-3 px-6 text-right">Porcentaje (%)</th>
                            <th class="py-3 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($impuestos as $impuesto)
                            <tr class="hover:bg-slate-50/70 transition-colors group">
                                <td class="py-3 px-4 text-center" @click.stop>
                                    <input type="checkbox" value="{{ $impuesto->id }}" wire:model.live="seleccionados" class="rounded border-slate-300 text-slate-900 shadow-sm cursor-pointer">
                                </td>
                                <td class="py-3 px-6 font-bold text-slate-900 flex items-center gap-2">
                                    {{ $impuesto->nombre }}
                                    @if($impuesto->activo)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">Activo</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">Inactivo</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-right font-medium text-slate-700">{{ number_format($impuesto->porcentaje, 2) }}%</td>
                                <td class="py-3 px-6 text-center">
                                    <button wire:click="edit({{ $impuesto->id }})" class="text-slate-400 hover:text-sovereign-blue transition-colors p-1" title="Editar">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500">No se encontraron impuestos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $impuestos->links() }}
            </div>
        </div>
    @elseif($view === 'form')
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $editando ? 'Editar Impuesto' : 'Nuevo Impuesto' }}</h1>
                <p class="text-sm text-slate-500 mt-1">{{ $editando ? 'Modifica la información del impuesto seleccionado.' : 'Completa el formulario para agregar un nuevo impuesto.' }}</p>
            </div>
            <button wire:click="cancelar" class="inline-flex items-center gap-2 bg-white border border-slate-300 text-slate-700 font-semibold text-sm px-4 py-2 rounded-xl hover:bg-slate-50 shadow-sm transition-all">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Volver a la lista
            </button>
        </div>

        <div class="bg-white rounded-card border border-slate-200 shadow-sm overflow-hidden">
            <form wire:submit="save">
                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre</label>
                            <input type="text" wire:model="form.nombre" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sovereign-blue focus:border-sovereign-blue outline-none transition-all">
                            @error('form.nombre') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Porcentaje (%)</label>
                            <input type="number" step="0.01" wire:model="form.porcentaje" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sovereign-blue focus:border-sovereign-blue outline-none transition-all">
                            @error('form.porcentaje') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <div class="flex items-center gap-3">
                                <button type="button" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-sovereign-blue focus:ring-offset-2 {{ $form->activo ? 'bg-sovereign-blue' : 'bg-slate-200' }}" role="switch" aria-checked="{{ $form->activo ? 'true' : 'false' }}" wire:click="$toggle('form.activo')">
                                    <span aria-hidden="true" class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $form->activo ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                                <div>
                                    <span class="block text-sm font-semibold text-slate-700 cursor-pointer" wire:click="$toggle('form.activo')">Activo</span>
                                    <span class="block text-xs text-slate-500">¿El impuesto estará disponible para asignar a productos?</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 md:px-8 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" wire:click="cancelar" class="px-5 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-sovereign-blue rounded-xl hover:bg-slate-800 transition-colors flex items-center gap-2">
                        <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[18px]">save</span>
                        <span wire:loading wire:target="save" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                        Guardar Impuesto
                    </button>
                </div>
            </form>
        </div>
    @endif
    
    <x-modal name="eliminar-masivo" wire:model="modalEliminarMasivoVisible" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-bold text-slate-900">Eliminar impuestos</h2>
            <p class="mt-1 text-sm text-slate-600">
                ¿Estás seguro de que deseas eliminar los impuestos seleccionados? Esta acción no se puede deshacer y podría afectar las facturas existentes si estaban asociados a ellas.
            </p>
            <div class="mt-6 flex justify-end gap-3">
                <button wire:click="$set('modalEliminarMasivoVisible', false)" type="button" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                    Cancelar
                </button>
                <button wire:click="eliminarMasivo" type="button" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </x-modal>
</x-settings-layout>
</div>
