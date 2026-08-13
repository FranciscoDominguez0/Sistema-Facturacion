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
        <!-- Header -->
        <div class="px-6 pb-6 pt-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-white flex items-center justify-center text-sovereign-blue">
                <span class="material-symbols-outlined text-lg">receipt_long</span>
            </div>
            <div>
                <h1 class="text-lg font-bold">VigiFact</h1>
                <p class="text-[10px] text-white/70 uppercase tracking-widest mt-0.5">Enterprise ERP</p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-2 py-4 space-y-0.5 scrollbar-hide">
            
            <a href="{{ route('dashboard') }}" class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}" wire:navigate>
                <span class="material-symbols-outlined text-[20px] transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'text-white/70 group-hover:text-white' }}" style="{{ request()->routeIs('dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">home</span>
                <span class="truncate">Inicio</span>
            </a>

            <a href="#" class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-white/70 hover:bg-white/10 hover:text-white">
                <span class="material-symbols-outlined text-[20px] transition-colors text-white/70 group-hover:text-white">group</span>
                <span class="truncate">Clientes</span>
            </a>
            
            <a href="#" class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-white/70 hover:bg-white/10 hover:text-white">
                <span class="material-symbols-outlined text-[20px] transition-colors text-white/70 group-hover:text-white">inventory_2</span>
                <span class="truncate">Productos</span>
            </a>
            
            <a href="#" class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-white/70 hover:bg-white/10 hover:text-white">
                <span class="material-symbols-outlined text-[20px] transition-colors text-white/70 group-hover:text-white">receipt_long</span>
                <span class="truncate">Facturas</span>
            </a>

            <a href="#" class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-white/70 hover:bg-white/10 hover:text-white">
                <span class="material-symbols-outlined text-[20px] transition-colors text-white/70 group-hover:text-white">badge</span>
                <span class="truncate">Vendedores</span>
            </a>

            <a href="#" class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-white/70 hover:bg-white/10 hover:text-white">
                <span class="material-symbols-outlined text-[20px] transition-colors text-white/70 group-hover:text-white">receipt</span>
                <span class="truncate">Gastos</span>
            </a>

            <div class="pt-4 pb-1">
                <div class="h-px w-full bg-white/10 rounded-full"></div>
            </div>

            <a href="#" class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-white/70 hover:bg-white/10 hover:text-white">
                <span class="material-symbols-outlined text-[20px] transition-colors text-white/70 group-hover:text-white">business</span>
                <span class="truncate">Empresa</span>
            </a>

        </nav>

        <!-- Footer Actions -->
        <div class="px-2 py-3 border-t border-white/10 mt-auto">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all text-white/70 hover:bg-white/10 hover:text-white">
                    <span class="material-symbols-outlined text-[20px] transition-colors text-white/70 group-hover:text-white">logout</span>
                    <span class="truncate">Cerrar sesión</span>
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
            <div class="hidden lg:block"></div> <!-- Spacer for desktop -->

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

    @livewireScripts
</body>
</html>
