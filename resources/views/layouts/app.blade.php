<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'VigiFact') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

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
<body class="font-sans antialiased bg-slate-50 text-slate-900 h-screen flex overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-slate-900/50 lg:hidden" @click="sidebarOpen = false"></div>

    <!-- SideNavBar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="bg-sovereign-blue text-white h-screen w-64 flex flex-col py-2 flex-shrink-0 shadow-xl z-30 fixed lg:relative lg:translate-x-0 transition-transform duration-300">
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
            
            <a href="{{ route('dashboard') }}" class="group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}" wire:navigate>
                <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white shadow-sm' : 'bg-white/10 text-white/70 group-hover:bg-white/20 group-hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">home</span>
                </div>
                <span class="text-sm tracking-wide {{ request()->routeIs('dashboard') ? 'font-bold' : 'font-medium' }}">Inicio</span>
            </a>

            <a href="{{ route('clientes') }}" class="group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all {{ request()->routeIs('clientes*') ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}" wire:navigate>
                <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all {{ request()->routeIs('clientes*') ? 'bg-white/20 text-white shadow-sm' : 'bg-white/10 text-white/70 group-hover:bg-white/20 group-hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('clientes*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">group</span>
                </div>
                <span class="text-sm tracking-wide {{ request()->routeIs('clientes*') ? 'font-bold' : 'font-medium' }}">Clientes</span>
            </a>
            
            <a href="{{ route('productos.index') }}" class="group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all {{ request()->routeIs('productos.*') ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}" wire:navigate>
                <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all {{ request()->routeIs('productos.*') ? 'bg-white/20 text-white shadow-sm' : 'bg-white/10 text-white/70 group-hover:bg-white/20 group-hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('productos.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">inventory_2</span>
                </div>
                <span class="text-sm tracking-wide {{ request()->routeIs('productos.*') ? 'font-bold' : 'font-medium' }}">Productos</span>
            </a>
            
            <a href="{{ route('facturas') }}" class="group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all {{ request()->routeIs('facturas*') ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}" wire:navigate>
                <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all {{ request()->routeIs('facturas*') ? 'bg-white/20 text-white shadow-sm' : 'bg-white/10 text-white/70 group-hover:bg-white/20 group-hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('facturas*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">receipt_long</span>
                </div>
                <span class="text-sm tracking-wide {{ request()->routeIs('facturas*') ? 'font-bold' : 'font-medium' }}">Facturas</span>
            </a>

            <a href="{{ route('vendedores') }}" class="group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all {{ request()->routeIs('vendedores*') ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}" wire:navigate>
                <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all {{ request()->routeIs('vendedores*') ? 'bg-white/20 text-white shadow-sm' : 'bg-white/10 text-white/70 group-hover:bg-white/20 group-hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('vendedores*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">badge</span>
                </div>
                <span class="text-sm tracking-wide {{ request()->routeIs('vendedores*') ? 'font-bold' : 'font-medium' }}">Vendedores</span>
            </a>

            <a href="{{ route('gastos') }}" class="group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all {{ request()->routeIs('gastos*') ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}" wire:navigate>
                <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all {{ request()->routeIs('gastos*') ? 'bg-white/20 text-white shadow-sm' : 'bg-white/10 text-white/70 group-hover:bg-white/20 group-hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('gastos*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">receipt</span>
                </div>
                <span class="text-sm tracking-wide {{ request()->routeIs('gastos*') ? 'font-bold' : 'font-medium' }}">Gastos</span>
            </a>

            <div class="pt-4 pb-2 px-2">
                <div class="h-px w-full bg-white/10 rounded-full"></div>
            </div>

            <a href="{{ route('empresa') }}" class="group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all {{ request()->routeIs('empresa') ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}" wire:navigate>
                <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all {{ request()->routeIs('empresa') ? 'bg-white/20 text-white shadow-sm' : 'bg-white/10 text-white/70 group-hover:bg-white/20 group-hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('empresa') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">business</span>
                </div>
                <span class="text-sm tracking-wide {{ request()->routeIs('empresa') ? 'font-bold' : 'font-medium' }}">Empresa</span>
            </a>

            @php
                $isConfigActive = request()->routeIs('configuracion.*') || request()->routeIs('usuarios.*') || request()->routeIs('roles.*') || request()->routeIs('seguridad.*');
            @endphp
            <a href="{{ route('configuracion.index') }}" class="group relative flex items-center gap-3 pl-1.5 pr-4 py-1.5 rounded-full transition-all {{ $isConfigActive ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}" wire:navigate>
                <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center transition-all {{ $isConfigActive ? 'bg-white/20 text-white shadow-sm' : 'bg-white/10 text-white/70 group-hover:bg-white/20 group-hover:text-white' }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ $isConfigActive ? 'font-variation-settings: \'FILL\' 1;' : '' }}">settings</span>
                </div>
                <span class="text-sm tracking-wide {{ $isConfigActive ? 'font-bold' : 'font-medium' }}">Configuración</span>
            </a>

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

    <!-- Main Content Area Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- TopNavBar -->
        <header class="bg-white w-full h-16 border-b border-slate-200 flex justify-between items-center px-4 md:px-8 flex-shrink-0 z-10">
            <!-- Left: Menu Icon for collapse -->
            <button @click="sidebarOpen = true" class="p-2 -ml-2 text-slate-500 hover:bg-slate-100 rounded-full lg:hidden focus:outline-none">
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
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    <x-toast />
    @livewireScripts
</body>
</html>
