{{-- Ítem enlace dentro de un <x-dropdown>. Acepta href, wire:click, wire:navigate, etc.
     Uso: <x-dropdown-link href="{{ route('perfil') }}">Mi perfil</x-dropdown-link> --}}
<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out']) }}>{{ $slot }}</a>
