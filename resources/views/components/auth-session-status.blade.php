{{-- Mensaje de estado de sesión para páginas de autenticación (ej. "Enviamos tu enlace de recuperación").
     Uso: <x-auth-session-status :status="session('status')" /> --}}
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
