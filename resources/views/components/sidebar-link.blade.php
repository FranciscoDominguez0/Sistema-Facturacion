{{-- Enlace del menú lateral. El estado activo se calcula en el servidor (primera
     pintada correcta al recargar) y Alpine lo mantiene actualizado al navegar.
     Los bindings :class usan sintaxis de OBJETO (no strings): así Alpine quita
     las clases activas pintadas por el servidor cuando el enlace deja de estar
     activo (con strings no las quita y el highlight se queda en la opción vieja).
     Props: href (requerido), icono (ícono de Material Symbols), secciones (prefijos
     de URL que activan el enlace).
     Uso: <x-sidebar-link href="{{ route('clientes') }}" icono="group" :secciones="['/clientes']">Clientes</x-sidebar-link> --}}
@props(['href', 'icono', 'secciones' => []])

@php
// Estado activo según la URL actual (servidor): evita que al recargar la página
// el sidebar pinte primero sin resaltado y luego Alpine lo aplique.
$activo = collect($secciones)->contains(function ($seccion) {
    $seccion = ltrim($seccion, '/');

    return request()->is($seccion) || request()->is($seccion.'/*');
});

// Clases estáticas del estado inicial (las mismas que evalúa Alpine al navegar).
$clasesEnlace = $activo
    ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10'
    : 'text-white/70 hover:bg-white/10 hover:text-white';

$clasesIcono = $activo
    ? 'bg-white/20 text-white shadow-sm'
    : 'bg-white/10 text-white/70 group-hover:bg-white/20 group-hover:text-white';

$estiloIcono = $activo ? "font-variation-settings: 'FILL' 1;" : '';

$pesoEtiqueta = $activo ? 'font-bold' : 'font-medium';

// Condición Alpine: la URL actual coincide con alguna sección.
$expresionActiva = '[' . collect($secciones)
    ->map(fn ($seccion) => "'" . $seccion . "'")
    ->implode(', ')
    . '].some(seccion => currentPath === seccion || currentPath.startsWith(seccion + \'/\'))';

// Objetos de clases condicionales: cada clase se agrega si la condición da true
// y se QUITA si da false (aunque venga pintada en el HTML del servidor).
$objetoClasesEnlace = "{
    'bg-white/15': {$expresionActiva},
    'text-white': {$expresionActiva},
    'shadow-sm': {$expresionActiva},
    'ring-1': {$expresionActiva},
    'ring-white/10': {$expresionActiva},
    'text-white/70': !({$expresionActiva}),
    'hover:bg-white/10': !({$expresionActiva}),
    'hover:text-white': !({$expresionActiva}),
}";

$objetoClasesIcono = "{
    'bg-white/20': {$expresionActiva},
    'text-white': {$expresionActiva},
    'shadow-sm': {$expresionActiva},
    'bg-white/10': !({$expresionActiva}),
    'text-white/70': !({$expresionActiva}),
    'group-hover:bg-white/20': !({$expresionActiva}),
    'group-hover:text-white': !({$expresionActiva}),
}";

$objetoPesoEtiqueta = "{ 'font-bold': {$expresionActiva}, 'font-medium': !({$expresionActiva}) }";
@endphp

<a
    href="{{ $href }}"
    wire:navigate
    @click="currentPath = new URL($el.href).pathname; abierto = false"
    :class="{{ $objetoClasesEnlace }}"
    :aria-current="{{ $expresionActiva }} ? 'page' : null"
    class="group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all {{ $clasesEnlace }}"
    @if ($activo) aria-current="page" @endif
>
    <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all {{ $clasesIcono }}"
         :class="{{ $objetoClasesIcono }}">
        <span class="material-symbols-outlined text-[20px]"
              :style="{{ $expresionActiva }} ? 'font-variation-settings: \'FILL\' 1;' : ''"
              @if ($activo) style="{{ $estiloIcono }}" @endif>{{ $icono }}</span>
    </div>
    <span class="text-sm tracking-wide {{ $pesoEtiqueta }}"
          :class="{{ $objetoPesoEtiqueta }}">{{ $slot }}</span>
</a>