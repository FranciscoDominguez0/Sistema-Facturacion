@props(['active' => 'facturacion'])

<div class="flex items-center gap-8 overflow-x-auto bg-slate-50 px-6 rounded-xl border border-slate-200 mb-6">


    <a href="{{ route('configuracion.facturacion') }}" wire:navigate class="py-4 text-sm font-medium transition-colors flex items-center gap-2 relative {{ $active === 'facturacion' ? 'text-slate-900 font-semibold' : 'text-slate-500 hover:text-slate-900' }}">
        <span class="material-symbols-outlined text-[18px] {{ $active === 'facturacion' ? 'text-sovereign-blue' : '' }}">receipt_long</span>
        Facturación
        @if($active === 'facturacion')
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-sovereign-blue rounded-full"></span>
        @endif
    </a>
    
    <a href="{{ route('usuarios.index') }}" wire:navigate class="py-4 text-sm font-medium transition-colors flex items-center gap-2 relative {{ $active === 'usuarios' ? 'text-slate-900 font-semibold' : 'text-slate-500 hover:text-slate-900' }}">
        <span class="material-symbols-outlined text-[18px] {{ $active === 'usuarios' ? 'text-sovereign-blue' : '' }}">group</span>
        Usuarios
        @if($active === 'usuarios')
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-sovereign-blue rounded-full"></span>
        @endif
    </a>

    <a href="{{ route('roles.index') }}" wire:navigate class="py-4 text-sm font-medium transition-colors flex items-center gap-2 relative {{ $active === 'roles' ? 'text-slate-900 font-semibold' : 'text-slate-500 hover:text-slate-900' }}">
        <span class="material-symbols-outlined text-[18px] {{ $active === 'roles' ? 'text-sovereign-blue' : '' }}">admin_panel_settings</span>
        Roles y permisos
        @if($active === 'roles')
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-sovereign-blue rounded-full"></span>
        @endif
    </a>

    <a href="{{ route('seguridad.index') }}" wire:navigate class="py-4 text-sm font-medium transition-colors flex items-center gap-2 relative {{ $active === 'seguridad' ? 'text-slate-900 font-semibold' : 'text-slate-500 hover:text-slate-900' }}">
        <span class="material-symbols-outlined text-[18px] {{ $active === 'seguridad' ? 'text-sovereign-blue' : '' }}">security</span>
        Seguridad
        @if($active === 'seguridad')
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-sovereign-blue rounded-full"></span>
        @endif
    </a>
</div>
