<div class="w-full pb-10">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Gastos', 'url' => route('gastos')],
            ['title' => $gasto->concepto]
        ]" />
    @endsection

    <!-- Header Navigation & Master Actions -->
    <div class="flex flex-col gap-6 mb-8 mt-4">
        <div>
            <a class="inline-flex items-center gap-2 text-slate-500 hover:text-sovereign-blue transition-colors" href="{{ route('gastos') }}">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span class="text-sm font-medium">Volver a gastos</span>
            </a>
        </div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Detalle del gasto</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-500 text-xs font-semibold tracking-wider uppercase">
                        GASTO #{{ str_pad($gasto->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="confirmarEliminacion" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-red-100 hover:text-red-700 transition-colors shadow-sm" type="button">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    <span class="text-sm font-medium">Eliminar</span>
                </button>
                <button class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-sovereign-blue text-white hover:bg-opacity-90 transition-colors shadow-sm" type="button">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    <span class="text-sm font-medium">Editar gasto</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Hero Highlight Ledger Card -->
    <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-8 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7 flex flex-col gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-semibold tracking-wider uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-sovereign-blue"></span>
                        {{ $gasto->categoria }}
                    </span>
                </div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">
                    {{ $gasto->concepto }}
                </h2>
                <div class="flex items-center gap-2 text-slate-500">
                    <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                    <span class="text-sm font-medium">{{ $gasto->fecha->translatedFormat('d \d\e F \d\e Y') }}</span>
                </div>
            </div>
            <div class="lg:col-span-5 flex flex-col lg:items-end justify-center bg-slate-50 lg:bg-transparent p-6 lg:p-0 rounded-lg">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                    Monto
                </span>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-bold text-sovereign-blue tracking-tighter">
                        {{ $gasto->monto_formateado }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Specifications Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
        <!-- Left Column: Detailed Information -->
        <div class="lg:col-span-7 flex flex-col gap-6">
            <div class="rounded-xl bg-white border border-slate-200 p-6 shadow-sm flex flex-col">
                <div class="flex items-center justify-between pb-5 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-sovereign-blue text-[20px]">feed</span>
                        <h2 class="text-lg font-semibold text-slate-800">Información del gasto</h2>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        ID: {{ str_pad($gasto->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                    <!-- Concepto -->
                    <div class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Concepto</span>
                        <p class="text-sm text-slate-800 font-medium">
                            {{ $gasto->concepto }}
                        </p>
                    </div>
                    <!-- Categoría -->
                    <div class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Categoría</span>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-slate-100 text-slate-700 text-sm font-medium">
                                <span class="material-symbols-outlined text-[16px]">shopping_bag</span>
                                {{ $gasto->categoria }}
                            </span>
                        </div>
                    </div>
                    <!-- Monto -->
                    <div class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Monto</span>
                        <p class="text-sm font-bold text-slate-800">
                            {{ $gasto->monto_formateado }}
                        </p>
                    </div>
                    <!-- Fecha del gasto -->
                    <div class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha del gasto</span>
                        <div class="flex items-center gap-2 text-slate-800">
                            <span class="material-symbols-outlined text-[18px] text-slate-500">event</span>
                            <span class="text-sm font-medium">{{ $gasto->fecha->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <!-- Registrado por -->
                    <div class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Registrado por</span>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-sovereign-blue text-white flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr($gasto->registradoPor->name, 0, 2)) }}
                            </div>
                            <span class="text-sm text-slate-800 font-medium">{{ $gasto->registradoPor->name }}</span>
                        </div>
                    </div>
                    <!-- Fecha de registro -->
                    <div class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha de registro</span>
                        <div class="flex items-center gap-2 text-slate-800">
                            <span class="material-symbols-outlined text-[18px] text-slate-500">schedule</span>
                            <span class="text-sm">{{ $gasto->created_at->format('d/m/Y — h:i A') }}</span>
                        </div>
                    </div>
                    <!-- Estado de comprobante -->
                    <div class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado de comprobante</span>
                        <div class="flex items-center gap-2">
                            @if($gasto->comprobante)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-100">
                                <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                                Comprobante adjunto
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-medium border border-slate-200">
                                <span class="material-symbols-outlined text-[16px]">info</span>
                                Sin adjunto
                            </span>
                            @endif
                        </div>
                    </div>
                    <!-- Última actualización -->
                    <div class="flex flex-col gap-1.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Última actualización</span>
                        <div class="flex items-center gap-2 text-slate-500">
                            <span class="material-symbols-outlined text-[18px]">update</span>
                            <span class="text-sm">{{ $gasto->updated_at->format('d/m/Y — h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Receipt & Validation -->
        <div class="lg:col-span-5 flex flex-col gap-6">
            <div class="rounded-xl bg-white border border-slate-200 p-6 shadow-sm flex flex-col h-full">
                <div class="flex items-center justify-between pb-5 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-sovereign-blue text-[20px]">receipt</span>
                        <h2 class="text-lg font-semibold text-slate-800">Comprobante</h2>
                    </div>
                </div>
                @if($gasto->comprobante)
                <div class="p-6 rounded-lg bg-slate-50 border border-slate-200 flex flex-col items-center justify-center h-full text-center gap-3">
                    <span class="material-symbols-outlined text-4xl text-sovereign-blue">tag</span>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1">Referencia</h4>
                        <p class="text-lg font-bold text-slate-800">{{ $gasto->comprobante }}</p>
                    </div>
                </div>
                @else
                <div class="p-10 rounded-lg bg-slate-50 border border-slate-200 flex flex-col items-center justify-center h-full text-center gap-3">
                    <span class="material-symbols-outlined text-4xl text-slate-300">receipt_long</span>
                    <div>
                        <h4 class="text-slate-700 font-semibold mb-1">Sin referencia</h4>
                        <p class="text-sm text-slate-500">Este gasto no tiene un número de comprobante.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-modal-danger show="confirmingDeletion" title="Eliminar Gasto">
        <p class="text-sm text-slate-600 mb-6">
            ¿Estás seguro de que deseas eliminar este gasto por <strong>{{ $gasto->monto_formateado }}</strong>? Esta acción no se puede deshacer y los registros financieros serán afectados.
        </p>
        
        <div class="flex justify-end gap-3">
            <button type="button" x-on:click="show = false" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Cancelar
            </button>
            <button type="button" x-on:click="show = false" wire:click="eliminar" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors flex items-center gap-2" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="eliminar" class="material-symbols-outlined text-[18px]">delete</span>
                <span wire:loading wire:target="eliminar" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                Eliminar
            </button>
        </div>
    </x-modal-danger>
</div>
