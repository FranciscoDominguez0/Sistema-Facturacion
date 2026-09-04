@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => null],
    ]" />
@endsection

<div>
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6">
        <div class="flex flex-col gap-1.5">
            <h1 class="text-3xl text-slate-900 tracking-tight font-bold">Configuración</h1>
            <p class="text-sm text-slate-500">Administra las opciones generales del sistema, accesos corporativos e identidades de usuario.</p>
        </div>
        <div class="flex items-center gap-3 self-start md:self-auto">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 text-xs font-semibold uppercase">
                <span class="material-symbols-outlined text-[16px] text-sovereign-blue">sync</span>
                <span>Última sincronización: Hoy, 10:42 AM</span>
            </div>
        </div>
    </div>

    @include('livewire.configuracion.partials.tabs', ['active' => 'facturacion'])

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Tarjeta de Facturación/Empresa -->
        <a href="{{ route('configuracion.facturacion') }}" wire:navigate class="group block p-6 rounded-xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-all hover:border-sovereign-blue/30">
            <div class="w-12 h-12 rounded-lg bg-slate-50 flex items-center justify-center text-sovereign-blue mb-4 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[24px]">receipt_long</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Numeración de Facturas</h3>
            <p class="text-sm text-slate-500">Configura la serie y correlativos de tu empresa.</p>
        </a>

        <!-- Tarjeta de Usuarios -->
        <a href="{{ route('usuarios.index') }}" wire:navigate class="group block p-6 rounded-xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-all hover:border-sovereign-blue/30">
            <div class="w-12 h-12 rounded-lg bg-slate-50 flex items-center justify-center text-sovereign-blue mb-4 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[24px]">group</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Usuarios</h3>
            <p class="text-sm text-slate-500">Administra las cuentas de usuario, asigna roles y gestiona accesos.</p>
        </a>

        <!-- Tarjeta de Roles -->
        <a href="{{ route('roles.index') }}" wire:navigate class="group block p-6 rounded-xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-all hover:border-sovereign-blue/30">
            <div class="w-12 h-12 rounded-lg bg-slate-50 flex items-center justify-center text-sovereign-blue mb-4 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[24px]">admin_panel_settings</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Roles y Permisos</h3>
            <p class="text-sm text-slate-500">Controla las acciones permitidas y la matriz de permisos por perfil.</p>
        </a>


    </div>
</div>
