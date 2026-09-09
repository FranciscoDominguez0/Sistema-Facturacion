{{-- Enlaces de paginación: centrados, en español y sin texto adicional.
     Recibe $paginator (y $elements, que Laravel inyecta).
     Uso: {{ $paginator->links('pagination.listado') }} --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación">
        <ul class="flex items-center gap-1.5">
            {{-- Anterior --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" class="inline-flex items-center justify-center h-9 px-3 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" wire:navigate rel="prev" class="inline-flex items-center justify-center h-9 px-3 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">Anterior</a>
                @endif
            </li>

            {{-- Números de página --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="inline-flex items-center justify-center h-9 min-w-9 px-2 rounded-lg text-sm text-slate-400">...</span>
                    </li>
                @elseif (!empty($element))
                    @foreach ($element as $pagina => $url)
                        @if ($pagina == $paginator->currentPage())
                            <li aria-current="page">
                                <span class="inline-flex items-center justify-center h-9 min-w-9 px-2 rounded-lg text-sm font-semibold bg-sovereign-blue text-white shadow-sm">{{ $pagina }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" wire:navigate class="inline-flex items-center justify-center h-9 min-w-9 px-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">{{ $pagina }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" wire:navigate rel="next" class="inline-flex items-center justify-center h-9 px-3 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">Siguiente</a>
                @else
                    <span aria-disabled="true" class="inline-flex items-center justify-center h-9 px-3 rounded-lg text-sm font-medium text-slate-300 cursor-not-allowed">Siguiente</span>
                @endif
            </li>
        </ul>
    </nav>
@endif