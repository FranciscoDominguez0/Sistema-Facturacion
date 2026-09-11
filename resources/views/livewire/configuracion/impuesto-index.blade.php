@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => route('settings.empresa')],
        ['title' => 'Impuestos', 'url' => null],
    ]" />
@endsection

<x-settings-layout activa="impuestos">
    @if($view === 'list')
        <x-listado
            titulo="Impuestos"
            metodo-crear="create"
            texto-crear="Nuevo Impuesto"
            :seleccionados="$seleccionados"
            :ids-pagina="$this->idsPagina"
            :paginador="$impuestos"
        >
            <x-slot name="cabeceras">
                <th class="py-3 px-4 font-medium text-slate-500 w-1/2">
                    <div class="flex items-center gap-1 cursor-pointer">
                        Nombre
                    </div>
                </th>
                <th class="py-3 px-4 font-medium text-slate-500">
                    <div class="flex items-center gap-1 cursor-pointer">
                        Porcentaje (%)
                    </div>
                </th>
            </x-slot>

            <x-slot name="filas">
                @forelse($impuestos as $impuesto)
                <tr wire:key="impuesto-{{ $impuesto->id }}" wire:click="edit({{ $impuesto->id }})" class="hover:bg-slate-50 transition-colors cursor-pointer group">
                    <td class="py-4 px-4 text-center" @click.stop>
                        <input type="checkbox" value="{{ $impuesto->id }}" wire:model.live="seleccionados" class="rounded border-slate-300 text-slate-900 shadow-sm cursor-pointer">
                    </td>
                    <td class="py-4 px-4 text-blue-600 font-medium">
                        {{ $impuesto->nombre }}
                    </td>
                    <td class="py-4 px-4 text-slate-700">
                        {{ number_format($impuesto->porcentaje, 2) }}%
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-8 px-4 text-center text-slate-500">
                        No se encontraron impuestos.
                    </td>
                </tr>
                @endforelse
            </x-slot>
        </x-listado>
    @else
        <!-- Formulario Inline de Creación/Edición -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <button wire:click="cancelar" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-white transition-colors border border-transparent hover:border-slate-200 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </button>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">{{ $editando ? 'Editar Impuesto' : 'Nuevo Impuesto' }}</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Configura el nombre y porcentaje del impuesto.</p>
                    </div>
                </div>
                <button wire:click="save" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Guardar
                </button>
            </div>

            <div class="p-6">
                <div class="max-w-2xl space-y-6">
                    <div class="flex items-center gap-6">
                        <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Nombre <span class="text-red-500">*</span></label>
                        <div class="flex-1">
                            <input type="text" wire:model="form.nombre" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                            @error('form.nombre') <span class="mt-2 text-xs text-red-500 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-6">
                        <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Porcentaje (%)</label>
                        <div class="flex-1">
                            <input type="number" step="0.01" wire:model="form.porcentaje" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                            @error('form.porcentaje') <span class="mt-2 text-xs text-red-500 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mt-6">
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800">Activo</h4>
                            <p class="text-xs text-slate-500 mt-0.5">¿El impuesto estará disponible para asignar a productos?</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="form.activo" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-sovereign-blue/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sovereign-blue"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-modal-danger show="modalEliminarMasivoVisible" title="Eliminar impuestos">
        <p class="text-sm text-slate-600 mb-6">
            ¿Estás seguro de que deseas eliminar los impuestos seleccionados? Esta acción no se puede deshacer y podría afectar las facturas existentes si estaban asociados a ellas.
        </p>
        <div class="flex justify-end gap-3">
            <button wire:click="$set('modalEliminarMasivoVisible', false)" type="button" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancelar
            </button>
            <button x-on:click="show = false" wire:click="eliminarMasivo" type="button" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="eliminarMasivo" class="material-symbols-outlined text-[18px]">delete</span>
                <span wire:loading wire:target="eliminarMasivo" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                Sí, eliminar
            </button>
        </div>
    </x-modal-danger>
</x-settings-layout>
