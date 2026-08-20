<div class="w-full">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Gastos', 'url' => route('gastos')],
            ['title' => $gasto->concepto]
        ]" />
    @endsection

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-start justify-between gap-6">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <span class="px-2 py-1 bg-slate-100 text-slate-600 font-bold text-[11px] rounded uppercase tracking-wider border border-slate-200">
                    {{ $gasto->categoria }}
                </span>
                <span class="font-mono text-sm text-slate-500">ID: {{ $gasto->id }}</span>
            </div>
            <h1 class="text-3xl font-bold text-sovereign-blue mb-2">{{ $gasto->concepto }}</h1>
            <p class="text-slate-500 text-sm">Registrado por <span class="font-medium text-slate-700">{{ $gasto->registradoPor->name }}</span> el {{ $gasto->created_at->format('d M Y, H:i') }}</p>
        </div>
        {{-- Botón editar se agregará cuando exista la ruta gastos.editar --}}
    </div>

    <!-- Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Monto -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col justify-center shadow-sm">
            <h3 class="font-bold text-[11px] text-slate-500 uppercase tracking-wider mb-2">Monto</h3>
            <div class="text-3xl font-bold text-sovereign-blue">{{ $gasto->monto_formateado }}</div>
        </div>

        <!-- Fecha del gasto -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col justify-center shadow-sm">
            <h3 class="font-bold text-[11px] text-slate-500 uppercase tracking-wider mb-2">Fecha del Gasto</h3>
            <div class="text-2xl font-bold text-slate-800">{{ $gasto->fecha->format('d M Y') }}</div>
            <div class="text-sm text-slate-500 mt-1">{{ $gasto->fecha->translatedFormat('l') }}</div>
        </div>

        <!-- Comprobante -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col justify-center shadow-sm">
            <h3 class="font-bold text-[11px] text-slate-500 uppercase tracking-wider mb-2">Comprobante</h3>
            @if($gasto->comprobante)
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">receipt</span>
                    <span class="text-base font-semibold text-slate-800">{{ $gasto->comprobante }}</span>
                </div>
            @else
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                    <span class="text-sm">Sin comprobante</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Detalle completo -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-800">Detalle del Gasto</h2>
        </div>
        <div class="divide-y divide-slate-100">
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider w-40 shrink-0">Concepto</span>
                <span class="text-sm text-slate-800 font-medium">{{ $gasto->concepto }}</span>
            </div>
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider w-40 shrink-0">Categoría</span>
                <span class="text-sm text-slate-800">{{ $gasto->categoria }}</span>
            </div>
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider w-40 shrink-0">Monto</span>
                <span class="text-sm text-slate-800 font-semibold">{{ $gasto->monto_formateado }}</span>
            </div>
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider w-40 shrink-0">Fecha</span>
                <span class="text-sm text-slate-800">{{ $gasto->fecha->format('d/m/Y') }}</span>
            </div>
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider w-40 shrink-0">Comprobante</span>
                <span class="text-sm text-slate-800">{{ $gasto->comprobante ?? '—' }}</span>
            </div>
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider w-40 shrink-0">Registrado por</span>
                <span class="text-sm text-slate-800">{{ $gasto->registradoPor->name }}</span>
            </div>
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider w-40 shrink-0">Creado</span>
                <span class="text-sm text-slate-500">{{ $gasto->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-1">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider w-40 shrink-0">Actualizado</span>
                <span class="text-sm text-slate-500">{{ $gasto->updated_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>
