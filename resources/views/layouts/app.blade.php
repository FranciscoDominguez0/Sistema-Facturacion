<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'VigiFact') }}</title>

    <!-- Iconos: fuente autoalojada en /fonts (declarada en app.css). El preload
         la pide en paralelo y el script evita ver las ligaduras como texto. -->
    <link rel="preload" href="/fonts/material-symbols/material-symbols-outlined.woff2" as="font" type="font/woff2" crossorigin>

    <script>
        if (document.fonts) {
            document.documentElement.classList.add('fonts-cargando');
            document.fonts.ready.then(() => document.documentElement.classList.remove('fonts-cargando'));
        }
    </script>

    <style>
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            font-feature-settings: 'liga';
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }

        /* Utilidad para ocultar scrollbar */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Estilo general para scrollbars de la app */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
    </style>

    <!-- Scripts -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>

    <script>
        // Impide que el navegador restaure desde la caché una página autenticada
        // tras cerrar sesión (botón "atrás" / BFCache).
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 h-screen flex overflow-hidden">

    @persist('sidebar')
    <!-- SideNavBar: el estado Alpine vive en este contenedor persistido para que
         no se pierda al navegar (SPA) ni dependa de elementos que se recrean -->
    <div
        x-data="{ abierto: false, currentPath: window.location.pathname }"
        x-on:livewire:navigated.document="currentPath = window.location.pathname; abierto = false"
        x-on:abrir-sidebar.window="abierto = true"
        class="flex-shrink-0"
    >
        <!-- Mobile sidebar backdrop: hermano del aside para que no tape sus enlaces -->
        <div x-show="abierto" x-transition.opacity class="fixed inset-0 z-20 bg-slate-900/50 lg:hidden" style="display: none;" @click="abierto = false"></div>

        <aside
            :class="abierto ? 'translate-x-0' : '-translate-x-full'"
            class="bg-sovereign-blue text-white h-screen w-64 flex flex-col py-2 flex-shrink-0 shadow-xl z-30 fixed lg:relative lg:translate-x-0 transition-transform duration-300"
        >
        <!-- Header (Logo) -->
        <div class="px-6 pb-6 pt-4 flex items-center justify-center gap-3">
            <div class="h-10 w-10 bg-slate-900 rounded-full flex items-center justify-center text-white shadow-sm">
                <span class="material-symbols-outlined text-2xl">receipt_long</span>
            </div>
            <h1 class="text-2xl font-semibold text-white tracking-tight">
                Vigi<span class="font-light">Fact</span>
            </h1>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 scrollbar-hide">
            <x-sidebar-link href="{{ route('dashboard') }}" icono="home" :secciones="['/dashboard']">Inicio</x-sidebar-link>

            @canany(['clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar'])
                <x-sidebar-link href="{{ route('clientes') }}" icono="group" :secciones="['/clientes']">Clientes</x-sidebar-link>
            @endcanany

            @canany(['productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar'])
                <x-sidebar-link href="{{ route('productos.index') }}" icono="inventory_2" :secciones="['/productos']">Productos</x-sidebar-link>
            @endcanany

            @canany(['facturas.ver', 'facturas.crear', 'facturas.editar', 'facturas.eliminar'])
                <x-sidebar-link href="{{ route('facturas') }}" icono="receipt_long" :secciones="['/facturas']">Facturas</x-sidebar-link>
            @endcanany

            @canany(['gastos.ver', 'gastos.crear', 'gastos.editar', 'gastos.eliminar'])
                <x-sidebar-link href="{{ route('gastos') }}" icono="receipt" :secciones="['/gastos']">Gastos</x-sidebar-link>
            @endcanany

            @canany(['empresa.gestionar', 'usuarios.ver'])
                <div class="pt-4 pb-2 px-2">
                    <div class="h-px w-full bg-white/10 rounded-full"></div>
                </div>

                <x-sidebar-link href="{{ route('settings.empresa') }}" icono="settings" :secciones="['/settings', '/usuarios', '/roles']">Configuración</x-sidebar-link>
            @endcanany
        </nav>

        <!-- Footer Actions -->
        <div class="px-4 py-4 border-t border-white/10 mt-auto">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all hover:bg-white/5">
                    <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center bg-white/5 text-white/70 group-hover:bg-red-500 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </div>
                    <span class="text-sm tracking-wide text-white/70 font-medium group-hover:text-red-400 transition-colors">Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>
    </div>
    @endpersist

    <!-- Main Content Area Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- TopNavBar -->
        <header class="bg-white w-full h-16 border-b border-slate-200 flex justify-between items-center px-4 md:px-8 flex-shrink-0 z-10">
            <!-- Left: Menu Icon for collapse -->
            <button @click="$dispatch('abrir-sidebar')" class="p-2 -ml-2 text-slate-500 hover:bg-slate-100 rounded-full lg:hidden focus:outline-none">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <div class="flex-1 flex items-center ml-4 lg:ml-0 overflow-x-auto scrollbar-hide">
                @hasSection('breadcrumbs')
                    @yield('breadcrumbs')
                @endif
            </div> <!-- Spacer for desktop / Breadcrumbs -->

            <!-- Right: Trailing Actions -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-1 border-r border-slate-200 pr-4">
                    <button class="p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined text-[20px]">notifications</span>
                    </button>
                    <button class="p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors hidden sm:block">
                        <span class="material-symbols-outlined text-[20px]">help_outline</span>
                    </button>
                </div>

                <!-- Avatar Profile Dropdown -->
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen" @click.outside="dropdownOpen = false" class="flex items-center space-x-2 pl-2 focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-sovereign-blue text-white flex items-center justify-center font-bold text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</p>
                        </div>
                        <span class="material-symbols-outlined text-slate-400 text-[20px]">arrow_drop_down</span>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="dropdownOpen" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-slate-200 py-1" style="display: none;">
                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" wire:navigate>Perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-slate-100">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Canvas Area -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-8">
            <div class="w-full">
                {{ $slot }}
            </div>
        </main>
    </div>

    <x-toast />
    @livewireScripts
</body>
</html>
