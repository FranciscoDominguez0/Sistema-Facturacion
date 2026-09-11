{{-- Skeleton del sidebar: se superpone al menú real tras el login, igual que el
     skeleton del dashboard. Simula su estructura (logo, enlaces, divisor y
     botón de salir) sobre el fondo azul de la marca, con shimmer escalonado.
     Solo es decorativo: no recibe props ni interactúa. --}}
<div id="skeleton-sidebar" class="absolute inset-0 z-40 bg-sovereign-blue flex flex-col py-2 pointer-events-none transition-opacity duration-300" aria-hidden="true">
    {{-- Logo + nombre de la app --}}
    <div class="px-6 pb-6 pt-4 flex items-center justify-center gap-3 relative overflow-hidden">
        <div class="absolute inset-0 shimmer-bg"></div>
        <div class="h-10 w-10 bg-white/10 rounded-full relative z-10"></div>
        <div class="h-6 w-28 bg-white/10 rounded-lg relative z-10"></div>
    </div>

    {{-- Enlaces del menú --}}
    <nav class="flex-1 overflow-hidden px-4 py-6 space-y-1.5">
        @for ($i = 0; $i < 4; $i++)
            <div class="flex items-center gap-3 pl-1.5 pr-4 py-1.5 relative overflow-hidden">
                <div class="absolute inset-0 shimmer-bg stagger-{{ $i % 3 + 1 }}"></div>
                <div class="w-8 h-8 bg-white/10 rounded-full relative z-10"></div>
                <div class="h-3.5 w-24 bg-white/10 rounded-full relative z-10"></div>
            </div>
        @endfor

        {{-- Divisor antes de Configuración --}}
        <div class="pt-4 pb-2 px-2">
            <div class="h-px w-full bg-white/10 rounded-full"></div>
        </div>

        <div class="flex items-center gap-3 pl-1.5 pr-4 py-1.5 relative overflow-hidden">
            <div class="absolute inset-0 shimmer-bg stagger-2"></div>
            <div class="w-8 h-8 bg-white/10 rounded-full relative z-10"></div>
            <div class="h-3.5 w-24 bg-white/10 rounded-full relative z-10"></div>
        </div>
    </nav>

    {{-- Botón de cerrar sesión --}}
    <div class="px-4 py-4 border-t border-white/10">
        <div class="flex items-center gap-3 pl-1.5 pr-4 py-1.5 relative overflow-hidden">
            <div class="absolute inset-0 shimmer-bg stagger-1"></div>
            <div class="w-8 h-8 bg-white/10 rounded-full relative z-10"></div>
            <div class="h-3.5 w-24 bg-white/10 rounded-full relative z-10"></div>
        </div>
    </div>
</div>