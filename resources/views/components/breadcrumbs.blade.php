{{-- Migas de pan. Recibe un array de ítems con 'title' y 'url'. El último ítem es el actual (sin enlace).
     Uso: <x-breadcrumbs :links="[['title'=>'Clientes','url'=>route('clientes.index')],['title'=>'Editar']]" /> --}}
@props(['links' => []])

<nav class="flex items-center gap-2 text-sm" aria-label="Breadcrumb">
    <a href="{{ route('dashboard') }}" wire:navigate class="text-slate-500 hover:text-sovereign-blue transition-colors flex items-center justify-center" title="Inicio">
        <span class="material-symbols-outlined text-[20px]">home</span>
    </a>
    
    @foreach ($links as $link)
        <span class="text-slate-300 select-none">/</span>
        @if (!$loop->last && !empty($link['url']))
            <a href="{{ $link['url'] }}" wire:navigate class="text-slate-500 hover:text-sovereign-blue transition-colors font-medium">
                {{ $link['title'] }}
            </a>
        @else
            <span class="text-slate-800 font-medium">{{ $link['title'] }}</span>
        @endif
    @endforeach
</nav>
