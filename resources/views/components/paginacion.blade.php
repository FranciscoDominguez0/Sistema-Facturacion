{{-- Paginación reutilizable para listados: centrada, en español y sin texto adicional.
     Props: paginador (LengthAwarePaginator).
     Uso: <x-paginacion :paginador="$clientes" /> --}}
@props(['paginador'])

@if ($paginador->hasPages())
    <div class="flex justify-center py-3">
        {{ $paginador->links('pagination.listado') }}
    </div>
@endif