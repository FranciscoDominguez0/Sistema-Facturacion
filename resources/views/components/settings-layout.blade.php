{{-- Diseño compartido de las pantallas de Configuración: sidebar de navegación + contenido.
     Props: activa ('empresa' | 'facturacion' | 'usuarios'). --}}
@props(['activa' => ''])

<div class="flex flex-col lg:flex-row gap-6">
    <aside class="lg:w-64 flex-shrink-0">
        <nav class="flex flex-col gap-6">

            <!-- Configuración Básica -->
            <div>
                <h3 class="px-3 text-sm font-semibold text-slate-400 mb-2">Configuración Básica</h3>
                <ul class="space-y-0.5">
                    <li>
                        <a href="{{ route('settings.empresa') }}" wire:navigate
                            class="block w-full px-3 py-2 text-sm transition-all text-left rounded-lg {{ $activa === 'empresa' ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-50' }}">
                            Detalles de la Empresa
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('configuracion.impuestos') }}" wire:navigate
                            class="block w-full px-3 py-2 text-sm transition-all text-left rounded-lg {{ $activa === 'impuestos' ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-50' }}">
                            Impuestos
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Facturación -->
            <div>
                <h3 class="px-3 text-sm font-semibold text-slate-400 mb-2">Facturación</h3>
                <ul class="space-y-0.5">
                    <li>
                        <a href="{{ route('settings.facturacion') }}" wire:navigate
                            class="block w-full px-3 py-2 text-sm transition-all text-left rounded-lg {{ $activa === 'facturacion' ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-50' }}">
                            Numeración
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Acceso -->
            <div>
                <h3 class="px-3 text-sm font-semibold text-slate-400 mb-2">Acceso</h3>
                <ul class="space-y-0.5">
                    <li>
                        <a href="{{ route('settings.usuarios') }}" wire:navigate
                            class="block w-full px-3 py-2 text-sm transition-all text-left rounded-lg {{ $activa === 'usuarios' ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-50' }}">
                            Usuarios
                        </a>
                    </li>
                </ul>
            </div>

        </nav>
    </aside>

    <div class="flex-1 min-w-0">
        {{ $slot }}
    </div>
</div>