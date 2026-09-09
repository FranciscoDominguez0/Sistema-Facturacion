@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => route('settings.empresa')],
        ['title' => 'Usuarios', 'url' => null],
    ]" />
@endsection

<x-settings-layout activa="usuarios">
    @if($view === 'list')
        <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h2 class="text-xl text-slate-900 font-bold">Usuarios</h2>
                
                <div class="flex items-center gap-2 w-full md:w-auto">
                    @if(count($seleccionados) > 0)
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <div class="bg-slate-800 text-white text-sm px-4 py-2 rounded-md font-medium cursor-pointer flex items-center gap-2">
                                    Comportamiento
                                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                </div>
                            </x-slot>
                            <x-slot name="content">
                                <button wire:click="confirmarEliminacionMasiva" type="button" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    Eliminar
                                </button>
                            </x-slot>
                        </x-dropdown>
                    @endif
                    <div class="flex-1 md:w-64">
                        <input wire:model.live.debounce.300ms="search" class="w-full bg-white border border-slate-200 rounded-md px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-slate-300" placeholder="Filtrar" type="text"/>
                    </div>
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <div class="bg-white border border-slate-200 rounded-md px-3 py-2 text-sm text-slate-600 flex items-center gap-2 cursor-pointer">
                                Estado: {{ $filtroEstado ?: 'Todos' }}
                                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                            </div>
                        </x-slot>
                        <x-slot name="content">
                            <button wire:click="$set('filtroEstado', '')" type="button" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                Todos
                            </button>
                            <button wire:click="$set('filtroEstado', 'Activo')" type="button" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 font-medium flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Activo
                            </button>
                            <button wire:click="$set('filtroEstado', 'Inactivo')" type="button" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 font-medium flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Inactivo
                            </button>
                        </x-slot>
                    </x-dropdown>
                    <button wire:click="crearUsuario" type="button" class="bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-slate-800 transition-colors ml-2">
                        Nuevo Usuario
                    </button>
                </div>
            </div>

            <!-- Tabla Ejecutiva de Usuarios -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-white">
                            <th class="py-3 px-4 w-12 text-center">
                                <input type="checkbox" class="rounded border-slate-300 text-slate-900 shadow-sm cursor-pointer" wire:click="seleccionarTodos" @checked($idsPagina && count(array_diff($idsPagina, $seleccionados)) === 0)>
                            </th>
                            <th class="py-3 px-4 font-medium text-slate-500 w-1/2">
                                <div class="flex items-center gap-1 cursor-pointer">
                                    Nombre
                                    <div class="flex flex-col">
                                        <span class="material-symbols-outlined text-[10px] leading-none">expand_less</span>
                                        <span class="material-symbols-outlined text-[10px] leading-none -mt-1">expand_more</span>
                                    </div>
                                </div>
                            </th>
                            <th class="py-3 px-4 font-medium text-slate-500">
                                <div class="flex items-center gap-1 cursor-pointer">
                                    Correo
                                    <div class="flex flex-col">
                                        <span class="material-symbols-outlined text-[10px] leading-none">expand_less</span>
                                        <span class="material-symbols-outlined text-[10px] leading-none -mt-1">expand_more</span>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($usuarios as $usuario)
                        <tr wire:key="usuario-{{ $usuario->id }}" wire:click="editarUsuario({{ $usuario->id }})" class="hover:bg-slate-50 transition-colors cursor-pointer group">
                            <td class="py-4 px-4 text-center" @click.stop>
                                <input type="checkbox" value="{{ $usuario->id }}" wire:model.live="seleccionados" class="rounded border-slate-300 text-slate-900 shadow-sm cursor-pointer">
                            </td>
                            <td class="py-4 px-4 text-blue-600 font-medium">
                                {{ $usuario->name }}
                            </td>
                            <td class="py-4 px-4 text-slate-700">
                                {{ $usuario->email }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 px-4 text-center text-slate-500">
                                No se encontraron usuarios.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <!-- Paginación -->
                <x-paginacion :paginador="$usuarios" />
            </div>
        </div>
    @else
        <!-- Formulario Inline de Creación/Edición -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <button wire:click="volverAtras" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-white transition-colors border border-transparent hover:border-slate-200 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </button>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">{{ $form->usuario ? 'Editar Usuario' : 'Nuevo Usuario' }}</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Configura los detalles y permisos del usuario.</p>
                    </div>
                </div>
                <button wire:click="guardarUsuario" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Guardar
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-slate-200 px-6 overflow-x-auto">
                <button wire:click="$set('tab', 'detalles')" class="{{ $tab === 'detalles' ? 'border-sovereign-blue text-sovereign-blue font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }} flex-shrink-0 px-1 py-4 text-sm border-b-2 transition-all mr-8">
                    Detalles
                </button>
                <button wire:click="$set('tab', 'notificaciones')" class="{{ $tab === 'notificaciones' ? 'border-sovereign-blue text-sovereign-blue font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }} flex-shrink-0 px-1 py-4 text-sm border-b-2 transition-all mr-8">
                    Notificaciones
                </button>
                <button wire:click="$set('tab', 'permisos')" class="{{ $tab === 'permisos' ? 'border-sovereign-blue text-sovereign-blue font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }} flex-shrink-0 px-1 py-4 text-sm border-b-2 transition-all">
                    Permisos
                </button>
            </div>

            <div class="p-6">
                @if($tab === 'detalles')
                    <div class="max-w-2xl space-y-6">
                        <div class="flex items-center gap-6">
                            <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Nombre completo <span class="text-red-500">*</span></label>
                            <div class="flex-1">
                                <input type="text" wire:model="form.name" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                                <x-input-error :messages="$errors->get('form.name')" class="mt-2 text-xs" />
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-6">
                            <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Correo electrónico</label>
                            <div class="flex-1">
                                <input type="email" wire:model="form.email" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                                <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-xs" />
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Contraseña</label>
                            <div class="flex-1">
                                <input type="password" wire:model="form.password" placeholder="{{ $form->usuario ? 'Dejar en blanco para no cambiar' : '' }}" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                                <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-xs" />
                            </div>
                        </div>
                    </div>
                @elseif($tab === 'notificaciones')
                    <div class="max-w-2xl space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">Notificación de inicio de sesión</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Envía un correo electrónico notificando que se ha realizado un inicio de sesión.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" value="" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-sovereign-blue/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sovereign-blue"></div>
                            </label>
                        </div>
                    </div>
                @elseif($tab === 'permisos')
                    <div class="max-w-3xl space-y-6">
                        @foreach($roles as $r)
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">{{ $r->name }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Permite acceso y privilegios del rol {{ strtolower($r->name) }}.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="radio" wire:model="form.rol" value="{{ $r->name }}" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-sovereign-blue/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sovereign-blue"></div>
                            </label>
                        </div>
                        @endforeach
                        <x-input-error :messages="$errors->get('form.rol')" class="mt-2 text-xs" />

                        <!-- Matriz de Permisos Personalizados -->
                        <div class="mt-8 pt-8 border-t border-slate-200">
                            <h4 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wider">Permisos Personalizados Adicionales</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr>
                                            <th class="py-2 font-medium text-slate-500">Módulo</th>
                                            <th class="py-2 font-medium text-slate-500 text-center">Ver</th>
                                            <th class="py-2 font-medium text-slate-500 text-center">Crear</th>
                                            <th class="py-2 font-medium text-slate-500 text-center">Editar</th>
                                            <th class="py-2 font-medium text-slate-500 text-center">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <!-- Usuarios -->
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-3 text-slate-700 font-medium">Usuarios</td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="usuarios.ver" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="usuarios.crear" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="usuarios.editar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="usuarios.eliminar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                        </tr>
                                        <!-- Clientes -->
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-3 text-slate-700 font-medium">Clientes</td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="clientes.ver" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="clientes.crear" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="clientes.editar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="clientes.eliminar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                        </tr>
                                        <!-- Productos -->
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-3 text-slate-700 font-medium">Productos</td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="productos.ver" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="productos.crear" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="productos.editar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="productos.eliminar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                        </tr>
                                        <!-- Facturas -->
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-3 text-slate-700 font-medium">Facturas</td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="facturas.ver" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="facturas.crear" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="facturas.editar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="facturas.eliminar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                        </tr>
                                        <!-- Gastos -->
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-3 text-slate-700 font-medium">Gastos</td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="gastos.ver" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="gastos.crear" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="gastos.editar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="gastos.eliminar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                        </tr>
                                        <!-- Empresa -->
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-3 text-slate-700 font-medium">Empresa</td>
                                            <td class="py-3 text-center text-slate-300">—</td>
                                            <td class="py-3 text-center"><input type="checkbox" wire:model="form.permisos" value="empresa.gestionar" class="rounded border-slate-300 text-sovereign-blue shadow-sm cursor-pointer"></td>
                                            <td class="py-3 text-center text-slate-300">—</td>
                                            <td class="py-3 text-center text-slate-300">—</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

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

    <!-- Modal Eliminar Usuarios Seleccionados -->
    <x-modal-danger show="modalEliminarMasivoVisible" title="Eliminar Usuarios" maxWidth="sm">
        <p class="text-sm text-slate-600">
            ¿Estás seguro de que deseas eliminar los <strong>{{ count($seleccionados) }}</strong> usuario(s) seleccionados? Esta acción es irreversible.
        </p>
        
        <div class="mt-6 flex justify-end gap-4 pt-4 border-t border-slate-50">
            <button type="button" @click="show = false" class="px-5 py-2.5 bg-white border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                Cancelar
            </button>
            <button type="button" wire:click="eliminarSeleccionados" class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-sm" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="eliminarSeleccionados">Eliminar</span>
                <span wire:loading wire:target="eliminarSeleccionados">Eliminando...</span>
            </button>
        </div>
    </x-modal-danger>
</x-settings-layout>
