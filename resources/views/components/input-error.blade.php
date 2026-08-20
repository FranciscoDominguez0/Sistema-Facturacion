{{-- Muestra en rojo los errores de validación de un campo. Si no hay errores, no renderiza nada.
     Uso: <x-input-error :messages="$errors->get('form.email')" class="mt-1" /> --}}
@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
