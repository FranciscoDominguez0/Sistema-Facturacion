@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => route('settings.empresa')],
        ['title' => 'Usuarios', 'url' => null],
    ]" />
@endsection

<x-settings-layout activa="usuarios">
    @if($view === 'list')
        <x-listado
            titulo="Usuarios"
            metodo-crear="crearUsuario"
            texto-crear="Nuevo Usuario"
            :seleccionados="$seleccionados"
            :ids-pagina="$idsPagina"
            :paginador="$usuarios"
        >
            <x-slot name="filtros">
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
            </x-slot>

            <x-slot name="cabeceras">
                <th class="py-3 px-4 font-medium text-slate-500 w-1/2">
                    Nombre
                </th>
                <th class="py-3 px-4 font-medium text-slate-500">
                    Correo
                </th>
            </x-slot>

            <x-slot name="filas">
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
            </x-slot>
        </x-listado>
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
