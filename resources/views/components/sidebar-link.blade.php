{{-- Enlace del menú lateral. Detecta el enlace activo según la URL actual en el cliente
     (Alpine) para no re-renderizar el sidebar persistido al navegar y evitar el parpadeo.
     Props: href (requerido), icono (ícono de Material Symbols), secciones (prefijos de
     URL que activan el enlace).
     Uso: <x-sidebar-link href="{{ route('clientes') }}" icono="group" :secciones="['/clientes']">Clientes</x-sidebar-link> --}}
@props(['href', 'icono', 'secciones' => []])

@php
// Expresión Alpine que marca el enlace si la URL actual coincide con alguna sección.
$expresionActiva = '[' . collect($secciones)
    ->map(fn ($seccion) => "'" . $seccion . "'")
    ->implode(', ')
    . '].some(seccion => currentPath === seccion || currentPath.startsWith(seccion + \'/\'))';
@endphp

<a
    href="{{ $href }}"
    wire:navigate
    @click="currentPath = new URL($el.href).pathname; abierto = false"
    :class="{{ $expresionActiva }} ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white'"
    :aria-current="{{ $expresionActiva }} ? 'page' : null"
    class="group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all"
>
    <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all"
         :class="{{ $expresionActiva }} ? 'bg-white/20 text-white shadow-sm' : 'bg-white/10 text-white/70 group-hover:bg-white/20 group-hover:text-white'">
        <span class="material-symbols-outlined text-[20px]"
              :style="{{ $expresionActiva }} ? 'font-variation-settings: \'FILL\' 1;' : ''">{{ $icono }}</span>
    </div>
    <span class="text-sm tracking-wide"
          :class="{{ $expresionActiva }} ? 'font-bold' : 'font-medium'">{{ $slot }}</span>
</a>