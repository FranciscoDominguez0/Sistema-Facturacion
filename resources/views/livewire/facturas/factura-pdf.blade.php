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

            <x-boton-descarga href="{{ route('facturas.pdf', $factura->id) }}" primary>
                <span class="material-symbols-outlined text-[18px]">download</span>
                Descargar
            </x-boton-descarga>
        </div>
    </div>

    <!-- Vista previa del PDF: ocupa toda la pantalla disponible -->
    <div x-data="{ loaded: false }" class="relative bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" style="min-height: 650px;">
        
        <!-- Skeleton Loader -->
        <div x-show="!loaded" class="absolute inset-0 z-10 bg-slate-50/50 p-6 md:p-12 flex flex-col items-center overflow-hidden">
            <!-- Simulación del esqueleto de una factura A4 -->
            <div class="w-full max-w-[794px] h-[1123px] bg-white border border-slate-200 shadow-sm flex flex-col p-8 animate-pulse">
                <!-- Header Skeleton -->
                <div class="flex justify-between w-full mb-12">
                    <div class="w-1/3">
                        <div class="h-10 w-3/4 bg-slate-200 rounded mb-4"></div>
                        <div class="h-4 w-1/2 bg-slate-100 rounded mb-2"></div>
                        <div class="h-4 w-2/3 bg-slate-100 rounded"></div>
                    </div>
                    <div class="w-1/4 flex justify-end">
                        <div class="h-20 w-full bg-slate-200 rounded-l-3xl"></div>
                    </div>
                </div>

                <!-- Info Row Skeleton -->
                <div class="flex justify-between w-full mb-10">
                    <div class="w-1/3">
                        <div class="h-3 w-1/4 bg-slate-200 rounded mb-4"></div>
                        <div class="h-6 w-3/4 bg-slate-200 rounded mb-2"></div>
                        <div class="h-4 w-1/2 bg-slate-100 rounded mb-4"></div>
                        <div class="space-y-2">
                            <div class="h-3 w-full bg-slate-100 rounded"></div>
                            <div class="h-3 w-5/6 bg-slate-100 rounded"></div>
                        </div>
                    </div>
                    <div class="w-1/3 flex justify-end items-start">
                        <div class="w-3/4 h-24 bg-slate-100 rounded-xl"></div>
                    </div>
                </div>

                <!-- Table Skeleton -->
                <div class="w-full mb-10">
                    <div class="w-full h-10 bg-slate-200 rounded-full mb-4"></div>
                    <div class="space-y-3">
                        <div class="w-full h-12 bg-slate-50 rounded-full"></div>
                        <div class="w-full h-12 bg-slate-50 rounded-full"></div>
                        <div class="w-full h-12 bg-slate-50 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <iframe x-ref="pdfIframe" src="{{ route('facturas.preview', $factura->id) }}"
            @load="loaded = true"
            style="width: 100%; height: calc(100vh - 13rem); min-height: 650px;"
            class="bg-white block relative z-0"
            title="Vista previa de la factura"></iframe>
    </div>
</div>