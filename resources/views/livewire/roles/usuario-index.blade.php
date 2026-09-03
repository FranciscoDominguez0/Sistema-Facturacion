@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => route('configuracion.index')],
        ['title' => 'Usuarios', 'url' => null],
    ]" />
@endsection

<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-2">
            </div>
            <h1 class="text-3xl text-slate-900 tracking-tight font-bold">Configuración de usuarios</h1>
            <p class="text-sm text-slate-500">Gestión de accesos, roles y credenciales de los integrantes de la organización.</p>
        </div>
        <div class="flex items-center gap-3 self-start md:self-auto">
            <button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-sovereign-blue text-white font-medium shadow-sm hover:bg-slate-800 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Nuevo usuario</span>
            </button>
        </div>
    </div>

    @include('livewire.configuracion.partials.tabs', ['active' => 'usuarios'])

    <div class="flex flex-col gap-6">
        <!-- Barra de Herramientas y Filtros -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="md:col-span-6 flex items-center gap-2 bg-slate-50 px-3 py-2 rounded-lg border border-slate-200">
                <span class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
                <input wire:model.live.debounce.300ms="search" class="bg-transparent border-none outline-none text-sm text-slate-900 placeholder:text-slate-400 w-full" placeholder="Buscar por nombre, correo..." type="text"/>
            </div>
            <div class="md:col-span-3">
                <div class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-100 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">badge</span>
                        <span class="text-sm text-slate-700">Rol: Todos los roles</span>
                    </div>
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">expand_more</span>
                </div>
            </div>
            <div class="md:col-span-3">
                <div class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-100 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">filter_alt</span>
                        <span class="text-sm text-slate-700">Estado: Todos los estados</span>
                    </div>
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">expand_more</span>
                </div>
            </div>
        </div>

        <!-- Tabla Ejecutiva de Usuarios -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="py-3.5 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider">Usuario</th>
                            <th class="py-3.5 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider">Correo electrónico</th>
                            <th class="py-3.5 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider">Rol</th>
                            <th class="py-3.5 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                            <th class="py-3.5 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider">Último acceso</th>
                            <th class="py-3.5 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($usuarios as $usuario)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-sovereign-blue text-white font-bold flex items-center justify-center text-xs tracking-wider shadow-sm">
                                        {{ strtoupper(substr($usuario->name, 0, 2)) }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-bold text-slate-900 truncate">{{ $usuario->name }}</span>
                                        <span class="text-slate-500 text-xs truncate">Usuario del sistema</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-700">{{ $usuario->email }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-sovereign-blue text-xs uppercase font-semibold border border-blue-100">
                                    <span class="material-symbols-outlined text-[14px]">shield_person</span>
                                    Administrador
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Activo
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col">
                                    <span class="font-medium text-slate-900">N/A</span>
                                    <span class="text-slate-500 uppercase text-[10px] tracking-wider">Sin registro</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <button class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Opciones" type="button">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-slate-500">
                                No se encontraron usuarios con ese criterio.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pie de Tabla y Paginación -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>
</div>
