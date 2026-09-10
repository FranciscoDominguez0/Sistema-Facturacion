<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Iconos: fuente autoalojada en /fonts (declarada en app.css). El preload
             la pide en paralelo y el script evita ver las ligaduras como texto. -->
        <link rel="preload" href="/fonts/material-symbols/material-symbols-outlined.woff2" as="font" type="font/woff2" crossorigin>

        <script>
            if (document.fonts) {
                document.documentElement.classList.add('fonts-cargando');
                document.fonts.ready.then(() => document.documentElement.classList.remove('fonts-cargando'));
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center font-sans selection:bg-sovereign-blue selection:text-white">
        {{ $slot }}
    </body>
</html>
