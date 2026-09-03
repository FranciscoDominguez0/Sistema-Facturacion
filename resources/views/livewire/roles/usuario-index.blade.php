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
            <h1 class="text-3xl text-slate-900 tracking-tight font-bold">Configuración de usuarios</h1>
            <p class="text-sm text-slate-500">Gestión de accesos, roles y credenciales de los integrantes de la organización.</p>
        </div>
        <div class="flex items-center gap-3 self-start md:self-auto">
            <button wire:click="abrirModal" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-sovereign-blue text-white font-medium shadow-sm hover:bg-slate-800 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Nuevo usuario</span>
            </button>
        </div>
    </div>

    @include('livewire.configuracion.partials.tabs', ['active' => 'usuarios'])

    <div class="flex flex-col gap-6">
        <!-- Barra de Herramientas y Filtros -->
        <div class="flex flex-col md:flex-row gap-3 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex-1 flex items-center gap-2 bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 focus-within:border-sovereign-blue focus-within:ring-1 focus-within:ring-sovereign-blue transition-all">
                <span class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
                <input wire:model.live.debounce.300ms="search" class="bg-transparent border-none outline-none text-sm text-slate-900 placeholder:text-slate-400 w-full p-0 focus:ring-0" placeholder="Buscar por nombre, correo..." type="text"/>
            </div>
            <div class="flex-1 flex items-center gap-2 bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 focus-within:border-sovereign-blue focus-within:ring-1 focus-within:ring-sovereign-blue transition-all">
                <span class="material-symbols-outlined text-slate-400 text-[18px]">badge</span>
                <select wire:model.live="filtroRol" class="bg-transparent border-none outline-none text-sm text-slate-700 w-full p-0 focus:ring-0 cursor-pointer">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                    @endforeach
                </select>
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
                        <tr wire:key="usuario-{{ $usuario->id }}" class="hover:bg-slate-50 transition-colors group">
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
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Opciones" type="button">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <button type="button" wire:click="editarUsuario({{ $usuario->id }})" class="w-full text-left flex items-center gap-2 block w-full px-4 py-2 text-start text-sm leading-5 text-slate-700 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 transition duration-150 ease-in-out">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                            Editar usuario
                                        </button>
                                        <button type="button" wire:click="confirmarEliminacion({{ $usuario->id }})" class="w-full text-left flex items-center gap-2 block w-full px-4 py-2 text-start text-sm leading-5 text-red-600 hover:bg-red-50 focus:outline-none focus:bg-red-50 transition duration-150 ease-in-out">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                            Eliminar usuario
                                        </button>
                                    </x-slot>
                                </x-dropdown>
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

    <!-- Modal Crear/Editar Usuario -->
    <x-modal-action show="modalVisible" :title="$tituloModal" maxWidth="md">
        <form wire:submit="guardarUsuario">
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1" for="name">Nombre completo <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="form.name" id="name" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                    <x-input-error :messages="$errors->get('form.name')" class="mt-2 text-xs" />
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1" for="email">Correo electrónico <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="form.email" id="email" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-xs" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1" for="password">Contraseña {{ $form->usuario ? '(Dejar en blanco para no cambiar)' : '' }} <span class="text-red-500">*</span></label>
                    <input type="password" wire:model="form.password" id="password" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-xs" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1" for="rol">Rol asignado <span class="text-red-500">*</span></label>
                    <select wire:model="form.rol" id="rol" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors">
                        <option value="">Seleccione un rol...</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.rol')" class="mt-2 text-xs" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-4 pt-4 border-t border-slate-50">
                <button type="button" @click="show = false" class="px-5 py-2.5 bg-white border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-sovereign-blue text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors shadow-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="guardarUsuario">Guardar Usuario</span>
                    <span wire:loading wire:target="guardarUsuario">Guardando...</span>
                </button>
            </div>
        </form>
    </x-modal-action>

    <!-- Modal Eliminar Usuario -->
    <x-modal-danger show="modalEliminarVisible" title="Eliminar Usuario" maxWidth="sm">
        <p class="text-sm text-slate-600">
            ¿Estás seguro de que deseas eliminar este usuario? Esta acción es irreversible y el usuario perderá todo el acceso al sistema.
        </p>
        
        <div class="mt-6 flex justify-end gap-4 pt-4 border-t border-slate-50">
            <button type="button" @click="show = false" class="px-5 py-2.5 bg-white border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                Cancelar
            </button>
            <button type="button" wire:click="eliminarUsuario" class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-sm" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="eliminarUsuario">Eliminar</span>
                <span wire:loading wire:target="eliminarUsuario">Eliminando...</span>
            </button>
        </div>
    </x-modal-danger>
</div>
