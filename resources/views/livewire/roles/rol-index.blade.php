@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => route('settings.empresa')],
        ['title' => 'Roles y permisos', 'url' => null],
    ]" />
@endsection

<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6">
        <div class="flex flex-col gap-1.5">
            <h2 class="text-xl text-slate-900 tracking-tight font-bold">Roles y matriz de permisos</h2>
            <p class="text-sm text-slate-500">Define los privilegios operativos y restricciones de acceso para cada nivel de usuario de la entidad legal.</p>
        </div>
        <div class="flex items-center gap-3 self-start md:self-auto">
            <button type="button" wire:click="abrirModalRol" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-sovereign-blue text-white font-medium shadow-sm hover:bg-slate-800 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">add_moderator</span>
                <span>Crear rol</span>
            </button>
        </div>
    </div>

    <!-- Workspace Grid: Roles Selection & Permission Matrix -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Columna Izquierda: Lista de Roles -->
        <div class="lg:col-span-4 flex flex-col gap-4">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perfiles Registrados ({{ $roles->count() }})</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase">ACTIVO: {{ $rolActivo ? strtoupper($rolActivo->name) : 'NINGUNO' }}</span>
            </div>

            @foreach($roles as $rol)
                @if($rolActivoId === $rol->id)
                    <!-- Role Card (ACTIVE) -->
                    <div class="bg-white border-2 border-sovereign-blue p-5 rounded-xl shadow-md flex flex-col gap-3 relative overflow-hidden">
                        <div class="absolute top-0 left-0 bottom-0 w-1.5 bg-sovereign-blue"></div>
                        <div class="flex items-start justify-between pl-1">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sovereign-blue text-[20px]">{{ $rol->name === 'Administrador' ? 'shield_person' : ($rol->name === 'Vendedor' ? 'storefront' : 'person') }}</span>
                                <span class="text-lg font-bold text-slate-900">{{ $rol->name }}</span>
                            </div>
                            <span class="text-[10px] font-bold bg-blue-50 text-sovereign-blue border border-blue-100 px-2 py-0.5 rounded uppercase tracking-wider">MODO EDICIÓN</span>
                        </div>
                        <p class="text-sm text-slate-700 leading-relaxed pl-1">Rol de {{ strtolower($rol->name) }} del sistema.</p>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 mt-1 pl-1">
                            <span class="text-sm font-semibold text-sovereign-blue flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">group</span>
                                {{ $rol->users_count }} usuario{{ $rol->users_count !== 1 ? 's' : '' }} asignado{{ $rol->users_count !== 1 ? 's' : '' }}
                            </span>
                            <span class="text-[10px] font-bold text-sovereign-blue uppercase tracking-wider">SELECCIONADO</span>
                        </div>
                    </div>
                @else
                    <!-- Role Card (INACTIVE) -->
                    <div wire:click="seleccionarRol({{ $rol->id }})" class="bg-slate-50 border border-slate-200 p-5 rounded-xl shadow-sm cursor-pointer hover:bg-slate-100 transition-colors flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-slate-700 text-[20px]">{{ $rol->name === 'Administrador' ? 'shield_person' : ($rol->name === 'Vendedor' ? 'storefront' : 'person') }}</span>
                                <span class="text-lg font-bold text-slate-900">{{ $rol->name }}</span>
                            </div>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed">Rol de {{ strtolower($rol->name) }} del sistema.</p>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-200 mt-1">
                            <span class="text-sm text-slate-500 font-medium flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-slate-400">group</span>
                                {{ $rol->users_count }} usuario{{ $rol->users_count !== 1 ? 's' : '' }} asignado{{ $rol->users_count !== 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                @endif
            @endforeach
            

        </div>

        <!-- Columna Derecha: Matriz de Permisos -->
        <div class="lg:col-span-8 flex flex-col gap-6 bg-white border border-slate-200 p-6 lg:p-8 rounded-xl shadow-sm">
            <!-- Card Matrix Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-sovereign-blue">
                        <span class="material-symbols-outlined text-[24px]">tune</span>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="text-xl font-bold text-slate-900">Permisos asignados: {{ $rolActivo ? $rolActivo->name : 'N/A' }}</h3>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">MATRIZ OPERATIVA DETALLADA</span>
                    </div>
                </div>
            </div>

            <!-- Permission Modules Stack -->
            <div class="flex flex-col gap-8">
                
                @forelse($permisosAgrupados as $grupo => $permisos)
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 px-4 py-2.5 rounded-lg">
                        <span class="material-symbols-outlined text-sovereign-blue text-[20px]">
                            {{ $grupo === 'facturas' ? 'receipt_long' : ($grupo === 'empresa' ? 'business' : 'tune') }}
                        </span>
                        <h4 class="text-base font-bold text-slate-900 capitalize">Módulo: {{ $grupo }}</h4>
                        <span class="ml-auto text-sm font-semibold text-slate-500">{{ count($permisos) }} permisos</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                        @foreach($permisos as $permiso)
                        <label class="flex items-start gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors shadow-sm">
                            <input type="checkbox" wire:model="permisosAsignados" value="{{ $permiso->name }}" class="mt-1 w-4 h-4 text-sovereign-blue rounded focus:ring-sovereign-blue"/>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-900">{{ ucfirst(str_replace(['.', '_'], ' ', $permiso->name)) }}</span>
                                <span class="text-xs text-slate-500 mt-0.5 text-balance">Acceso a {{ str_replace(['.', '_'], ' ', $permiso->name) }} en el sistema.</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="p-6 bg-slate-50 border border-slate-100 rounded-lg flex flex-col items-center justify-center text-center gap-3">
                    <span class="material-symbols-outlined text-[32px] text-slate-300">security_update_warning</span>
                    <p class="text-slate-500 text-sm">No hay permisos registrados en la base de datos.<br>Por favor, ejecuta los seeders de permisos.</p>
                </div>
                @endforelse
            </div>

            <!-- Card Footer Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200 mt-4">
                <button type="button" class="px-5 py-2.5 rounded-lg bg-transparent text-slate-500 text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Restablecer
                </button>
                <button type="button" wire:click="guardarPermisos" class="px-6 py-2.5 rounded-lg bg-sovereign-blue text-white text-sm font-bold hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    Guardar permisos
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Crear Rol -->
    <x-modal-action show="modalRolVisible" title="Crear Nuevo Rol" maxWidth="md">
        <form wire:submit="guardarRol">
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1" for="rol_name">Nombre del rol <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nuevoRolNombre" id="rol_name" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                    <x-input-error :messages="$errors->get('nuevoRolNombre')" class="mt-2 text-xs" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-4 pt-4 border-t border-slate-50">
                <button type="button" @click="show = false" class="px-5 py-2.5 bg-white border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-sovereign-blue text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors shadow-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="guardarRol">Guardar Rol</span>
                    <span wire:loading wire:target="guardarRol">Guardando...</span>
                </button>
            </div>
        </form>
    </x-modal-action>
</div>
