{{-- Campo <input> estilizado. Acepta todos los atributos HTML estándar (type, wire:model, required, etc.).
     Uso: <x-text-input wire:model="nombre" id="nombre" type="text" class="mt-1 block w-full" /> --}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
