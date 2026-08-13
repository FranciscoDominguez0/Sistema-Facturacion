<x-app-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-sovereign-blue mb-1">Dashboard</h2>
        <p class="text-slate-500">Resumen de tu negocio</p>
    </div>

    <!-- Empty state canvas ready for future content -->
    <div class="w-full h-96 border border-slate-200 rounded-xl bg-white shadow-sm flex flex-col items-center justify-center gap-6">
        <!-- Logo de la empresa (hardcodeado temporalmente) -->
        <img src="{{ asset('img/logo.png') }}" alt="Vigitec Panama" class="h-24 w-auto object-contain">
        
        <p class="text-slate-400 text-xs font-semibold uppercase tracking-widest">Canvas preparado para contenido futuro</p>
    </div>
</x-app-layout>
