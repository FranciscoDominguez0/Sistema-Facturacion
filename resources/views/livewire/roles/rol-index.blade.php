@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => route('configuracion.index')],
        ['title' => 'Roles y permisos', 'url' => null],
    ]" />
@endsection

<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-2">
            </div>
            <h1 class="text-3xl text-slate-900 tracking-tight font-bold">Roles y matriz de permisos</h1>
            <p class="text-sm text-slate-500">Define los privilegios operativos y restricciones de acceso para cada nivel de usuario de la entidad legal.</p>
        </div>
        <div class="flex items-center gap-3 self-start md:self-auto">
            <button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-sovereign-blue text-white font-medium shadow-sm hover:bg-slate-800 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">add_moderator</span>
                <span>Crear rol</span>
            </button>
        </div>
    </div>

    @include('livewire.configuracion.partials.tabs', ['active' => 'roles'])

    <!-- Workspace Grid: Roles Selection & Permission Matrix -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Columna Izquierda: Lista de Roles -->
        <div class="lg:col-span-4 flex flex-col gap-4">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perfiles Registrados (3)</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase">ACTIVO: VENDEDOR</span>
            </div>

            <!-- Role Card 1: Administrador -->
            <div class="bg-slate-50 border border-slate-200 p-5 rounded-xl shadow-sm cursor-pointer hover:bg-slate-100 transition-colors flex flex-col gap-3">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-700 text-[20px]">shield_person</span>
                        <span class="text-lg font-bold text-slate-900">Administrador</span>
                    </div>
                    <span class="text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded uppercase tracking-wider">Sistema</span>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed">Acceso irrestricto a todos los módulos contables, facturación, auditoría y configuraciones.</p>
                <div class="flex items-center justify-between pt-3 border-t border-slate-200 mt-1">
                    <span class="text-sm text-slate-500 font-medium flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">group</span>
                        1 usuario asignado
                    </span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nivel 00</span>
                </div>
            </div>

            <!-- Role Card 2: Vendedor (ACTIVE) -->
            <div class="bg-white border-2 border-sovereign-blue p-5 rounded-xl shadow-md flex flex-col gap-3 relative overflow-hidden">
                <div class="absolute top-0 left-0 bottom-0 w-1.5 bg-sovereign-blue"></div>
                <div class="flex items-start justify-between pl-1">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sovereign-blue text-[20px]">storefront</span>
                        <span class="text-lg font-bold text-slate-900">Vendedor</span>
                    </div>
                    <span class="text-[10px] font-bold bg-blue-50 text-sovereign-blue border border-blue-100 px-2 py-0.5 rounded uppercase tracking-wider">Modo Edición</span>
                </div>
                <p class="text-sm text-slate-700 leading-relaxed pl-1">Emisión de facturas, cotizaciones, gestión de clientes y catálogo de productos.</p>
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 mt-1 pl-1">
                    <span class="text-sm font-semibold text-sovereign-blue flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">group</span>
                        2 usuarios asignados
                    </span>
                    <span class="text-[10px] font-bold text-sovereign-blue uppercase tracking-wider">SELECCIONADO</span>
                </div>
            </div>

            <!-- Role Card 3: Usuario -->
            <div class="bg-slate-50 border border-slate-200 p-5 rounded-xl shadow-sm cursor-pointer hover:bg-slate-100 transition-colors flex flex-col gap-3">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-500 text-[20px]">person</span>
                        <span class="text-lg font-bold text-slate-900">Usuario</span>
                    </div>
                    <span class="text-[10px] font-bold bg-slate-200 text-slate-500 px-2 py-0.5 rounded uppercase tracking-wider">Operador</span>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed">Visualización de comprobantes, consulta de reportes asignados y registro de gastos básicos.</p>
                <div class="flex items-center justify-between pt-3 border-t border-slate-200 mt-1">
                    <span class="text-sm text-slate-500 font-medium flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">group</span>
                        1 usuario asignado
                    </span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nivel 02</span>
                </div>
            </div>
            
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg flex items-start gap-3 mt-2">
                <span class="material-symbols-outlined text-sovereign-blue text-[20px] mt-0.5">verified_user</span>
                <p class="text-sm text-slate-500">
                    Las modificaciones de privilegios en el rol <strong class="text-slate-900 font-semibold">Vendedor</strong> se aplicarán automáticamente tras la renovación de la sesión de los usuarios.
                </p>
            </div>
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
                        <h3 class="text-xl font-bold text-slate-900">Permisos asignados: Vendedor</h3>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">MATRIZ OPERATIVA DETALLADA</span>
                    </div>
                </div>
                <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-sovereign-blue text-white text-sm font-semibold hover:bg-slate-800 transition-colors shadow-sm self-start sm:self-auto">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Guardar permisos
                </button>
            </div>

            <!-- Permission Modules Stack -->
            <div class="flex flex-col gap-8">
                
                <!-- Module 1: Facturación & Ventas -->
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 px-4 py-2.5 rounded-lg">
                        <span class="material-symbols-outlined text-sovereign-blue text-[20px]">receipt_long</span>
                        <h4 class="text-base font-bold text-slate-900">Facturación & Ventas</h4>
                        <span class="ml-auto text-sm font-semibold text-slate-500">3 / 4 concedidos</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                        <label class="flex items-start gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors shadow-sm">
                            <input type="checkbox" checked class="mt-1 w-4 h-4 text-sovereign-blue rounded focus:ring-sovereign-blue"/>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-900">Ver facturas</span>
                                <span class="text-xs text-slate-500 mt-0.5">Acceso al historial de comprobantes de venta emitidos.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors shadow-sm">
                            <input type="checkbox" checked class="mt-1 w-4 h-4 text-sovereign-blue rounded focus:ring-sovereign-blue"/>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-900">Crear facturas</span>
                                <span class="text-xs text-slate-500 mt-0.5">Generación de nuevas facturas electrónicas y cotizaciones.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors shadow-sm">
                            <input type="checkbox" checked class="mt-1 w-4 h-4 text-sovereign-blue rounded focus:ring-sovereign-blue"/>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-900">Emitir notas de crédito</span>
                                <span class="text-xs text-slate-500 mt-0.5">Modificación o anulación parcial de montos en facturas activas.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 bg-slate-50 border border-slate-100 rounded-lg opacity-80 cursor-pointer hover:opacity-100 transition-opacity">
                            <input type="checkbox" class="mt-1 w-4 h-4 text-sovereign-blue rounded focus:ring-sovereign-blue"/>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm font-semibold text-slate-900">Anular facturas</span>
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">lock</span>
                                </div>
                                <span class="text-xs font-medium text-slate-500 mt-0.5">Requiere autorización superior para baja fiscal.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Module 2: Clientes & Directorio -->
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 px-4 py-2.5 rounded-lg">
                        <span class="material-symbols-outlined text-sovereign-blue text-[20px]">contacts</span>
                        <h4 class="text-base font-bold text-slate-900">Clientes & Directorio</h4>
                        <span class="ml-auto text-sm font-semibold text-slate-500">2 / 3 concedidos</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                        <label class="flex items-start gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors shadow-sm">
                            <input type="checkbox" checked class="mt-1 w-4 h-4 text-sovereign-blue rounded focus:ring-sovereign-blue"/>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-900">Ver clientes</span>
                                <span class="text-xs text-slate-500 mt-0.5">Lectura del registro general y estados de cartera.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors shadow-sm">
                            <input type="checkbox" checked class="mt-1 w-4 h-4 text-sovereign-blue rounded focus:ring-sovereign-blue"/>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-900">Crear y editar clientes</span>
                                <span class="text-xs text-slate-500 mt-0.5">Alta y actualización de datos de contacto o condiciones comerciales.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 bg-slate-50 border border-slate-100 rounded-lg opacity-80 cursor-pointer hover:opacity-100 transition-opacity">
                            <input type="checkbox" class="mt-1 w-4 h-4 text-sovereign-blue rounded focus:ring-sovereign-blue"/>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-900">Exportar base de datos completa</span>
                                <span class="text-xs text-slate-500 mt-0.5">Descarga masiva de listado en formato XLSX o CSV.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Card Footer Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200 mt-4">
                <button type="button" class="px-5 py-2.5 rounded-lg bg-transparent text-slate-500 text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Restablecer
                </button>
                <button type="button" class="px-6 py-2.5 rounded-lg bg-sovereign-blue text-white text-sm font-bold hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    Guardar permisos
                </button>
            </div>
        </div>
    </div>
</div>
