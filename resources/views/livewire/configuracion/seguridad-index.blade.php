@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => route('configuracion.index')],
        ['title' => 'Seguridad', 'url' => null],
    ]" />
@endsection

<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-2">
            </div>
            <h1 class="text-3xl text-slate-900 tracking-tight font-bold">Configuración de seguridad</h1>
            <p class="text-sm text-slate-500">Políticas de contraseñas, control de sesiones y protocolos de seguridad.</p>
        </div>
    </div>

    @include('livewire.configuracion.partials.tabs', ['active' => 'seguridad'])

    <!-- Visual Banner Informativo Ejecutivo -->
    <div class="relative overflow-hidden rounded-xl bg-slate-800 text-white p-6 md:p-8 shadow-sm mb-8">
        <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-sovereign-blue/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-start gap-4 max-w-2xl">
                <div class="p-3 bg-slate-700 rounded-xl text-white">
                    <span class="material-symbols-outlined text-[28px]">lock</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-slate-300 uppercase tracking-widest font-bold">ESTADO DEL PERÍMETRO</span>
                    <h2 class="text-xl font-bold text-white">Estándar de Seguridad Corporativa Activo</h2>
                    <p class="text-sm text-slate-300">
                        Las políticas globales protegen 48 puestos de trabajo y 12 terminales de facturación electrónica bajo cifrado AES-256 institucional.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0 bg-slate-700/80 px-4 py-3 rounded-lg border border-slate-600">
                <span class="material-symbols-outlined text-[20px] text-emerald-400">check_circle</span>
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase font-bold text-slate-300">CUMPLIMIENTO ISO/IEC 27001</span>
                    <span class="text-sm text-emerald-400 font-semibold">100% Homologado</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Principal de Ajustes -->
    <form class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Columna Izquierda -->
        <div class="lg:col-span-8 flex flex-col gap-8">
            <!-- 1. Tarjeta: Política de Contraseñas -->
            <section class="bg-white rounded-xl p-6 shadow-sm border border-slate-200 flex flex-col gap-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sovereign-blue text-[22px]">password</span>
                        <h3 class="text-lg font-bold text-slate-900">Política de contraseñas</h3>
                    </div>
                    <p class="text-sm text-slate-500">Parámetros exigidos para las credenciales de acceso de todos los colaboradores.</p>
                </div>
                
                <div class="flex flex-col gap-4 pt-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-lg bg-slate-50 border border-slate-100">
                        <div class="flex flex-col">
                            <span class="font-semibold text-slate-900 text-sm">Longitud mínima de contraseña</span>
                            <span class="text-xs text-slate-500">Recomendado para entornos auditables: 10 a 16 caracteres.</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center bg-white border border-slate-200 rounded-lg p-1">
                                <button type="button" class="w-8 h-8 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">remove</span>
                                </button>
                                <input type="number" value="10" min="8" max="32" class="w-14 text-center bg-transparent border-none outline-none font-bold text-slate-900"/>
                                <button type="button" class="w-8 h-8 rounded flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                </button>
                            </div>
                            <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">CARACTERES</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4 p-4 rounded-lg bg-slate-50 border border-slate-100">
                        <div class="flex flex-col pr-4">
                            <span class="font-semibold text-slate-900 text-sm">Requerir al menos un carácter especial</span>
                            <span class="text-xs text-slate-500">Admite símbolos estándar (! @ # $ % ^ & *).</span>
                        </div>
                        <input type="checkbox" checked class="rounded text-sovereign-blue focus:ring-sovereign-blue h-5 w-5"/>
                    </div>
                    
                    <div class="flex items-center justify-between gap-4 p-4 rounded-lg bg-slate-50 border border-slate-100">
                        <div class="flex flex-col pr-4">
                            <span class="font-semibold text-slate-900 text-sm">Requerir letras mayúsculas y minúsculas</span>
                            <span class="text-xs text-slate-500">Fuerza variación tipográfica para reducir vulnerabilidad a diccionarios.</span>
                        </div>
                        <input type="checkbox" checked class="rounded text-sovereign-blue focus:ring-sovereign-blue h-5 w-5"/>
                    </div>
                </div>
            </section>

            <!-- 2. Tarjeta: Gestión de Sesiones y Accesos -->
            <section class="bg-white rounded-xl p-6 shadow-sm border border-slate-200 flex flex-col gap-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sovereign-blue text-[22px]">devices_other</span>
                        <h3 class="text-lg font-bold text-slate-900">Gestión de sesiones y accesos</h3>
                    </div>
                    <p class="text-sm text-slate-500">Control de sesiones simultáneas y tiempos de inactividad operativa.</p>
                </div>
                
                <div class="flex flex-col gap-4 pt-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-lg bg-slate-50 border border-slate-100">
                        <div class="flex flex-col">
                            <label class="font-semibold text-slate-900 text-sm">Tiempo de inactividad para cierre de sesión automático</label>
                            <span class="text-xs text-slate-500">Desconecta estaciones desatendidas para prevenir usos no autorizados.</span>
                        </div>
                        <select class="min-w-[240px] bg-white border border-slate-200 px-4 py-2.5 rounded-lg text-sm text-slate-900 font-medium outline-none">
                            <option value="15">15 minutos</option>
                            <option value="30" selected>30 minutos (Recomendado)</option>
                            <option value="60">60 minutos (1 hora)</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between gap-4 p-4 rounded-lg bg-slate-50 border border-slate-100">
                        <div class="flex flex-col pr-4">
                            <span class="font-semibold text-slate-900 text-sm">Cerrar todas las demás sesiones activas al cambiar contraseña</span>
                            <span class="text-xs text-slate-500">Invalida cookies de autenticación en otros terminales de inmediato.</span>
                        </div>
                        <input type="checkbox" checked class="rounded text-sovereign-blue focus:ring-sovereign-blue h-5 w-5"/>
                    </div>
                    
                    <div class="flex items-center justify-between gap-4 p-4 rounded-lg bg-slate-50 border border-slate-100">
                        <div class="flex flex-col pr-4">
                            <span class="font-semibold text-slate-900 text-sm">Bloqueo preventivo de cuenta tras 5 intentos fallidos consecutivos</span>
                            <span class="text-xs text-slate-500">Aplica un enfriamiento temporal de 15 minutos o requerimiento de reinicio por admin.</span>
                        </div>
                        <input type="checkbox" checked class="rounded text-sovereign-blue focus:ring-sovereign-blue h-5 w-5"/>
                    </div>
                </div>
            </section>
        </div>

        <!-- Columna Derecha -->
        <div class="lg:col-span-4 flex flex-col gap-8">
            <!-- 3. Tarjeta: Sesión Actual y Auditoría -->
            <section class="bg-white rounded-xl p-6 shadow-sm border border-slate-200 flex flex-col gap-6">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sovereign-blue text-[22px]">fingerprint</span>
                        <h3 class="text-lg font-bold text-slate-900">Sesión actual y auditoría</h3>
                    </div>
                    <p class="text-xs text-slate-500">Información discreta de trazabilidad y verificación de integridad.</p>
                </div>
                
                <div class="flex flex-col gap-3">
                    <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-100 flex items-start gap-3">
                        <div class="p-2 rounded bg-slate-200/50 text-sovereign-blue shrink-0">
                            <span class="material-symbols-outlined text-[20px]">laptop_mac</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">DISPOSITIVO Y ORIGEN</span>
                            <span class="text-sm font-semibold text-slate-900 mt-0.5">Chrome en macOS</span>
                            <span class="text-xs text-slate-500 mt-0.5">IP: 190.140.22.84</span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-100 flex items-start gap-3">
                        <div class="p-2 rounded bg-slate-200/50 text-sovereign-blue shrink-0">
                            <span class="material-symbols-outlined text-[20px]">enhanced_encryption</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">ESTADO CRIPTOGRÁFICO</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-sovereign-blue"></span>
                                <span class="text-sm font-semibold text-slate-900">TLS 1.3 Autenticado</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-700">ÍNDICE DE POSTURA</span>
                        <span class="text-sm font-bold text-sovereign-blue">96 / 100</span>
                    </div>
                    <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full" style="width: 96%;"></div>
                    </div>
                    <p class="text-[11px] text-slate-500">Su configuración cumple con directivas financieras internacionales de no-repudio.</p>
                </div>
            </section>
        </div>

        <!-- Pie de Página -->
        <div class="lg:col-span-12 pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200">
            <div class="flex items-center gap-2 text-slate-500 text-xs">
                <span class="material-symbols-outlined text-[18px]">history</span>
                <span>Última modificación guardada el 14 de Octubre por Alejandro Morales</span>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button type="button" class="px-6 py-2.5 rounded-lg text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Restaurar predeterminados
                </button>
                <button type="button" class="px-6 py-2.5 rounded-lg bg-sovereign-blue text-white text-sm font-bold shadow-sm hover:opacity-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Guardar seguridad</span>
                </button>
            </div>
        </div>
    </form>
</div>
