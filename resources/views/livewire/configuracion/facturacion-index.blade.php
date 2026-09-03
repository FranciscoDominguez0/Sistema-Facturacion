@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => route('configuracion.index')],
        ['title' => 'Facturación', 'url' => null],
    ]" />
@endsection

<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-2">
            </div>
            <h1 class="text-3xl text-slate-900 tracking-tight font-bold">Configuración de facturación</h1>
            <p class="text-sm text-slate-500">Administra la serie y correlativos de las facturas que se emiten en el sistema.</p>
        </div>
        <div class="flex items-center gap-3 self-start md:self-auto">
            <button wire:click="guardar" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-sovereign-blue text-white font-medium shadow-sm hover:bg-slate-800 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Guardar cambios</span>
            </button>
        </div>
    </div>

    @include('livewire.configuracion.partials.tabs', ['active' => 'facturacion'])

    <div class="flex flex-col gap-8 animate-fade-in-up">
        <section>
            <h3 class="text-base font-semibold text-slate-700 mb-4">Numeración de facturas</h3>
            <div class="bg-white rounded-lg border border-slate-200 p-6 grid grid-cols-1 md:grid-cols-2 gap-8 shadow-sm">
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
                        <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-slate-200 opacity-60">
                            <span class="text-sm text-slate-500">Siguiente...</span>
                            <span class="font-medium text-slate-900">{{ $prefijo_factura }}{{ str_pad((int)$siguiente_numero_factura + 1, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
