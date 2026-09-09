@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => route('settings.empresa')],
        ['title' => 'Numeración', 'url' => null],
    ]" />
@endsection

<x-settings-layout activa="facturacion">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-5 flex items-center justify-between bg-white border-b border-slate-100">
            <h2 class="text-xl text-slate-900 font-bold">Numeración de facturas</h2>
            <button wire:click="guardar" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors shadow-sm whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Guardar cambios
            </button>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="flex flex-col gap-5">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Prefijo</label>
                        <input wire:model.live="prefijo_factura" class="bg-slate-50 border border-slate-300 rounded-lg px-4 py-2 text-sm text-slate-900 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue transition-all" type="text" placeholder="FAC-"/>
                        <span class="text-xs text-slate-400">Caracteres que preceden al número. Ej: FAC-, INV-, 2024-</span>
                        @error('prefijo_factura') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Siguiente Número</label>
                        <input wire:model.live="siguiente_numero_factura" class="bg-slate-50 border border-slate-300 rounded-lg px-4 py-2 text-sm text-slate-900 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue transition-all" type="number" min="1"/>
                        <span class="text-xs text-slate-400">Se incrementará automáticamente.</span>
                        @error('siguiente_numero_factura') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Vista Previa de Secuencia</label>
                    <div class="bg-slate-50 rounded-lg border border-slate-200 p-4 flex flex-col gap-3">
                        <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-slate-200 opacity-60">
                            <span class="text-sm text-slate-500">Factura anterior</span>
                            <span class="font-medium text-slate-900">{{ $prefijo_factura }}{{ str_pad(max(1, (int)$siguiente_numero_factura - 1), 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-blue-50 border border-blue-200 p-4 rounded-lg shadow-sm">
                            <span class="text-sm font-medium text-sovereign-blue">Próxima Factura</span>
                            <span class="font-bold text-lg text-sovereign-blue">{{ $this->numero_factura_preview }}</span>
                        </div>
                        </div>
                        <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-slate-200 opacity-60">
                            <span class="text-sm text-slate-500">Siguiente...</span>
                            <span class="font-medium text-slate-900">{{ $prefijo_factura }}{{ str_pad((int)$siguiente_numero_factura + 1, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-settings-layout>
