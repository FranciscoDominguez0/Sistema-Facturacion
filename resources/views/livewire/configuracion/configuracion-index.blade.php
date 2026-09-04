@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => null],
    ]" />
@endsection

<div x-data="{ seccion: $persist('empresa.detalles') }">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Configuración</h1>
        <p class="text-sm text-slate-500 mt-1">Administra tu empresa, facturación, usuarios y permisos del sistema.</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- Sidebar de Navegación -->
        <aside class="lg:w-64 flex-shrink-0">
            <nav class="flex flex-col gap-6">

                <!-- Empresa -->
                <div>
                    <h3 class="px-3 text-sm font-semibold text-slate-400 mb-2">Configuración Básica</h3>
                    <ul class="space-y-0.5">
                        <li>
                            <button @click="seccion = 'empresa'"
                                :class="seccion === 'empresa' ? 'text-slate-900 font-semibold' : 'text-slate-700 hover:text-slate-900'"
                                class="w-full px-3 py-2 text-sm transition-all text-left">
                                Detalles de la Empresa
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Facturación -->
                <div>
                    <h3 class="px-3 text-sm font-semibold text-slate-400 mb-2">Facturación</h3>
                    <ul class="space-y-0.5">
                        <li>
                            <button @click="seccion = 'facturacion'"
                                :class="seccion === 'facturacion' ? 'text-slate-900 font-semibold' : 'text-slate-700 hover:text-slate-900'"
                                class="w-full px-3 py-2 text-sm transition-all text-left">
                                Numeración
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Acceso -->
                <div>
                    <h3 class="px-3 text-sm font-semibold text-slate-400 mb-2">Acceso</h3>
                    <ul class="space-y-0.5">
                        <li>
                            <button @click="seccion = 'usuarios'"
                                :class="seccion === 'usuarios' ? 'text-slate-900 font-semibold' : 'text-slate-700 hover:text-slate-900'"
                                class="w-full px-3 py-2 text-sm transition-all text-left">
                                Usuarios
                            </button>
                        </li>
                    </ul>
                </div>

            </nav>
        </aside>

        <!-- Contenido dinámico -->
        <div class="flex-1 min-w-0">

            <!-- Empresa -->
            <div x-show="seccion === 'empresa'" x-cloak>
                @can('empresa.gestionar')
                    @livewire('configuracion.empresa-form', ['tab' => 'detalles'], key('empresa-form'))
                @else
                    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-500 shadow-sm">
                        <span class="material-symbols-outlined text-4xl mb-3 block text-slate-300">lock</span>
                        No tienes permisos para gestionar la empresa.
                    </div>
                @endcan
            </div>

            <!-- Facturación -->
            <div x-show="seccion === 'facturacion'" x-cloak>
                @livewire('configuracion.facturacion-index', [], key('facturacion-index'))
            </div>

            <!-- Usuarios -->
            <div x-show="seccion === 'usuarios'" x-cloak>
                @livewire('roles.usuario-index', [], key('usuario-index'))
            </div>

        </div>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
</div>
