{{-- Enlaces de paginación: en español, con íconos y sin texto adicional.
     Recibe $paginator (y $elements, que Laravel inyecta).
     Uso: {{ $paginator->links('pagination.listado') }} --}}
@if ($paginator->hasPages())
    @php
        // Livewire genera los enlaces con ruta relativa (sin "/" inicial) a partir
        // de la ruta original; el navegador los resuelve contra el directorio actual,
        // así que en rutas anidadas (/settings/usuarios) se duplica el prefijo.
        // Se normalizan a ruta absoluta para que siempre apunten a la raíz.
        $absoluta = fn (string $url): string => str_starts_with($url, '/') ? $url : '/'.$url;

        $base = 'inline-flex h-10 items-center justify-center gap-1 rounded-lg text-sm font-medium transition-colors';
    @endphp
    <nav role="navigation" aria-label="Paginación">
        <ul class="flex flex-wrap items-center justify-center gap-1.5">
            {{-- Anterior --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" class="{{ $base }} border border-slate-100 bg-slate-50 px-3.5 text-slate-300 cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        <span class="hidden sm:inline">Anterior</span>
                    </span>
                @else
                    <a href="{{ $absoluta($paginator->previousPageUrl()) }}" wire:navigate rel="prev" aria-label="Página anterior" class="{{ $base }} border border-slate-200 bg-white px-3.5 text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        <span class="hidden sm:inline">Anterior</span>
                    </a>
                @endif
            </li>

            {{-- Números de página --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="{{ $base }} min-w-11 px-2.5 text-slate-400">{{ $element }}</span>
                    </li>
                @elseif (!empty($element))
                    @foreach ($element as $pagina => $url)
                        @if ($pagina == $paginator->currentPage())
                            <li aria-current="page">
                                <span class="{{ $base }} min-w-10 border border-sovereign-blue bg-sovereign-blue px-2 font-semibold text-white shadow-sm">{{ $pagina }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $absoluta($url) }}" wire:navigate class="{{ $base }} min-w-11 px-2.5 text-slate-600 hover:bg-slate-100 hover:text-slate-900">{{ $pagina }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $absoluta($paginator->nextPageUrl()) }}" wire:navigate rel="next" aria-label="Página siguiente" class="{{ $base }} border border-slate-200 bg-white px-3.5 text-slate-600 hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900">
                        <span class="hidden sm:inline">Siguiente</span>
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                @else
                    <span aria-disabled="true" class="{{ $base }} border border-slate-100 bg-slate-50 px-3.5 text-slate-300 cursor-not-allowed">
                        <span class="hidden sm:inline">Siguiente</span>
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
