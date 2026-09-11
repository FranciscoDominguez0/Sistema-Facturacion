<div class="w-full space-y-6">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Facturas', 'url' => route('facturas')],
            ['title' => 'Editar Factura', 'url' => route('facturas.edit', $factura->id)],
            ['title' => 'PDF']
        ]" />
    @endsection

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-sovereign-blue/10 border border-sovereign-blue/10 flex items-center justify-center shadow-sm flex-shrink-0">
                <span class="material-symbols-outlined text-sovereign-blue text-[26px]">picture_as_pdf</span>
            </div>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $factura->numero_factura }}</h2>
                    @php
                        $badgeClasses = match($factura->estado->value) {
                            'Pagada' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'Anulada' => 'bg-red-100 text-red-800 border-red-200',
                            default => 'bg-amber-100 text-amber-800 border-amber-200',
                        };
                    @endphp
                    <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $badgeClasses }}">
                        {{ $factura->estado->value }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1 truncate">Vista previa del PDF · {{ $factura->cliente->nombre }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 md:justify-end">
            <button wire:click="enviarPorCorreo" type="button"
                class="h-10 px-4 border border-slate-200 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 transition-colors inline-flex items-center gap-2 shadow-sm whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px] text-slate-500">mail</span>
                Enviar por correo
            </button>

            <button type="button" x-data="{}" @click="$refs.pdfIframe.contentWindow.print()"
                class="h-10 px-4 border border-slate-200 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 transition-colors inline-flex items-center gap-2 shadow-sm whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px] text-slate-500">print</span>
                Imprimir
            </button>

            <a href="{{ route('facturas.pdf', $factura->id) }}"
                class="h-10 px-4 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors inline-flex items-center gap-2 shadow-sm whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Descargar
            </a>
        </div>
    </div>

    <!-- Vista previa del PDF: ocupa toda la pantalla disponible -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <iframe x-ref="pdfIframe" src="{{ route('facturas.pdf', $factura->id) }}?print=true"
            style="width: 100%; height: calc(100vh - 13rem); min-height: 650px;"
            class="bg-white block"
            title="Vista previa de la factura"></iframe>
    </div>
</div>