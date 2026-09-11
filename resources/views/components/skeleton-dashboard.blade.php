{{-- Skeleton del dashboard: se muestra al entrar tras el login mientras "carga" la
     página. Simula la estructura real (KPIs con sparkline, gráfico, actividad
     reciente y tabla de facturas) con un efecto shimmer y aparición escalonada.
     Solo es decorativo: no recibe props ni interactúa. --}}

<div class="bg-slate-50 flex flex-col gap-6 pointer-events-none" aria-hidden="true">
    {{-- Encabezado: título + selector de rango + botón de acción --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-2">
            <div class="h-8 w-64 bg-slate-200/70 rounded-xl shimmer-bg"></div>
            <div class="h-4 w-48 bg-slate-200/50 rounded-lg shimmer-bg stagger-1"></div>
        </div>
        <div class="flex items-center gap-3">
            <div class="h-9 w-64 bg-white border border-slate-200/60 rounded-xl shadow-sm shimmer-bg stagger-1"></div>
            <div class="h-9 w-36 bg-sovereign-blue/90 rounded-xl shimmer-bg stagger-2"></div>
        </div>
    </div>

    {{-- Fila de KPIs (4 tarjetas con sparkline) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @for ($i = 1; $i <= 4; $i++)
            <div class="h-[132px] bg-white rounded-card border border-slate-200/80 shadow-subtle p-4 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute inset-0 shimmer-bg stagger-{{ $i % 3 + 1 }}"></div>
                <div class="h-3 w-1/2 bg-slate-100 rounded-md relative z-10"></div>
                <div class="flex justify-between items-end relative z-10">
                    <div class="h-7 w-1/3 bg-slate-200/80 rounded-lg"></div>
                    <div class="h-5 w-10 bg-emerald-50 rounded-md"></div>
                </div>
                <div class="h-10 w-full bg-slate-50/70 rounded-lg relative z-10"></div>
            </div>
        @endfor
    </div>

    {{-- Gráfico principal (2/3) + Actividad reciente (1/3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 h-[360px] bg-white rounded-card border border-slate-200/80 shadow-subtle p-6 flex flex-col gap-6 relative overflow-hidden">
            <div class="absolute inset-0 shimmer-bg stagger-1"></div>
            <div class="flex items-start justify-between relative z-10">
                <div class="h-5 w-40 bg-slate-200/70 rounded-lg"></div>
                <div class="h-8 w-28 bg-white border border-slate-200/60 rounded-md"></div>
            </div>
            <div class="flex-1 bg-slate-50/70 border border-slate-100 rounded-2xl relative z-10"></div>
        </div>
        <div class="bg-white rounded-card border border-slate-200/80 shadow-subtle p-6 flex flex-col gap-4 relative overflow-hidden">
            <div class="absolute inset-0 shimmer-bg stagger-2"></div>
            <div class="h-5 w-36 bg-slate-200/70 rounded-lg relative z-10"></div>
            <div class="h-3 w-28 bg-slate-200/50 rounded-md relative z-10"></div>
            <div class="flex-1 space-y-3.5 relative z-10">
                @for ($i = 0; $i < 5; $i++)
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200/70 shrink-0"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-3 w-3/4 bg-slate-200/70 rounded"></div>
                            <div class="h-3 w-1/2 bg-slate-200/50 rounded"></div>
                        </div>
                        <div class="h-3 w-12 bg-slate-200/60 rounded"></div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Tabla de gestión de facturas --}}
    <div class="bg-white rounded-card border border-slate-200/80 shadow-subtle overflow-hidden">
        <div class="h-14 bg-slate-50/80 border-b border-slate-100 flex items-center px-6 gap-4 relative overflow-hidden">
            <div class="absolute inset-0 shimmer-bg stagger-2 opacity-50"></div>
            <div class="h-4 w-44 bg-slate-200/70 rounded-md relative z-10"></div>
            <div class="h-6 w-28 bg-slate-100 rounded-full relative z-10"></div>
            <div class="h-8 w-36 bg-white border border-slate-200/60 rounded-lg ml-auto relative z-10"></div>
        </div>
        <div class="p-6 space-y-4">
            @for ($i = 0; $i < 5; $i++)
                <div class="h-10 {{ $i % 2 === 0 ? 'bg-slate-50/80' : 'bg-white border border-slate-50' }} rounded-xl w-full"></div>
            @endfor
        </div>
    </div>
</div>