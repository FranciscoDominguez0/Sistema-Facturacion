{{-- Etiqueta <label> estilizada para campos de formulario. Acepta `for` y `value`.
     Uso: <x-input-label for="nombre" :value="'Nombre'" /> --}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
