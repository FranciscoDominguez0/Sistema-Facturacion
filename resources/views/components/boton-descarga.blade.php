{{-- Enlace de descarga reutilizable con estilos consistentes.
     Props:
       href     (string) URL de la ruta que genera la descarga.
       primary  (bool)   Si true usa el estilo azul principal; si no, blanco con borde.
       menu     (bool)   Si true usa el estilo de ítem de menú desplegable (ancho completo).
     Uso:
       <x-boton-descarga href="{{ route('facturas.pdf', $factura->id) }}" primary>
           Descargar
       </x-boton-descarga> --}}
@props([
    'href' => '#',
    'primary' => false,
    'menu' => false,
])

@php
    if ($menu) {
        $clases = 'w-full items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-sovereign-blue transition-colors';
    } elseif ($primary) {
        $clases = 'bg-sovereign-blue text-white hover:bg-slate-800';
    } else {
        $clases = 'border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300';
    }
@endphp

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => $menu ? $clases : 'inline-flex items-center gap-2 rounded-lg shadow-sm whitespace-nowrap transition-colors '.$clases]) }}>
    {{ $slot }}
</a>