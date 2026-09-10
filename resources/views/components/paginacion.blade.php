{{-- Paginación reutilizable para listados: pie centrado con los controles.
     Props: paginador (LengthAwarePaginator).
     Uso: <x-paginacion :paginador="$clientes" /> --}}
@props(['paginador'])

@if ($paginador->hasPages())
    <div class="flex justify-center border-t border-slate-200 bg-slate-50/60 px-6 py-4">
        {{ $paginador->links('pagination.listado') }}
    </div>
@endif
