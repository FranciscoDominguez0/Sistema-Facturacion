@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Perfil', 'url' => null],
    ]" />
@endsection

<div class="max-w-3xl mx-auto space-y-8 pb-12">
    <!-- Tarjeta de presentación -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-slate-900 via-sovereign-blue to-slate-800 relative">
            <!-- Patrón decorativo sutil -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
        </div>
        <div class="px-6 sm:px-8 pb-8 -mt-12">
            <div class="flex flex-col sm:flex-row sm:items-end gap-5">
                <!-- Foto de perfil squircle -->
                <div class="relative flex-shrink-0 group z-10" x-data="{}">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-[1.25rem] border-4 border-white bg-slate-50 shadow-md overflow-hidden flex items-center justify-center transition-transform group-hover:scale-[1.02]">
                        @if ($avatar_path_actual)
                            <img src="{{ Storage::url($avatar_path_actual) }}" class="w-full h-full object-cover" alt="Foto de perfil">
                        @else
                            <span class="material-symbols-outlined text-5xl text-slate-300">person</span>
                        @endif
                    </div>

                    <input type="file" x-ref="inputAvatar" wire:model="avatar" accept="image/png, image/jpeg, image/webp" class="hidden">
                    <button type="button" @click="$refs.inputAvatar.click()" title="Cambiar foto"
                        class="absolute inset-0 rounded-[1.25rem] bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-all duration-200 flex flex-col items-center justify-center text-white cursor-pointer backdrop-blur-sm">
                        <span class="material-symbols-outlined text-2xl mb-1">add_a_photo</span>
                        <span class="text-[10px] uppercase tracking-wider font-bold">Cambiar</span>
                    </button>

                    @if ($avatar_path_actual)
                        <button type="button" wire:click="eliminarAvatar" title="Quitar foto"
                            class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-white text-red-500 flex items-center justify-center shadow-md border border-slate-200 hover:bg-red-500 hover:text-white hover:border-red-500 transition-colors z-20">
                            <span class="material-symbols-outlined text-[14px]">close</span>
                        </button>
                    @endif
                </div>

                <div class="pb-1 min-w-0 flex-1">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight truncate">{{ $nombre }}</h2>
                    <p class="text-sm font-medium text-slate-500 truncate mt-0.5">{{ $email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información personal -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
        <div class="px-6 sm:px-8 py-5 border-b border-slate-100 bg-white">
            <h3 class="text-base font-bold text-slate-900">Información personal</h3>
            <p class="text-[13px] text-slate-500 mt-1">Actualiza tus datos de contacto básicos.</p>
        </div>

        <form wire:submit="actualizarPerfil">
            <div class="px-6 sm:px-8 py-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                    <label class="w-48 flex-shrink-0 text-[13px] font-semibold text-slate-700">Nombre completo <span class="text-red-500">*</span></label>
                    <div class="flex-1">
                        <input type="text" wire:model="nombre" class="w-full bg-slate-50 border border-slate-200/70 focus:bg-white focus:border-sovereign-blue focus:ring-2 focus:ring-sovereign-blue/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none transition-all">
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2 text-xs" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                    <label class="w-48 flex-shrink-0 text-[13px] font-semibold text-slate-700">Correo electrónico <span class="text-red-500">*</span></label>
                    <div class="flex-1">
                        <input type="email" wire:model="email" class="w-full bg-slate-50 border border-slate-200/70 focus:bg-white focus:border-sovereign-blue focus:ring-2 focus:ring-sovereign-blue/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none transition-all">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end px-6 sm:px-8 py-4 bg-slate-50/50 border-t border-slate-100">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-sovereign-blue transition-all shadow-sm hover:shadow active:scale-[0.98]">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    <!-- Contraseña -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
        <div class="px-6 sm:px-8 py-5 border-b border-slate-100 bg-white">
            <h3 class="text-base font-bold text-slate-900">Seguridad y Contraseña</h3>
            <p class="text-[13px] text-slate-500 mt-1">Usa una contraseña larga y aleatoria para mantener tu cuenta segura.</p>
        </div>

        <form wire:submit="actualizarPassword">
            <div class="px-6 sm:px-8 py-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                    <label class="w-48 flex-shrink-0 text-[13px] font-semibold text-slate-700">Contraseña actual</label>
                    <div class="flex-1">
                        <input type="password" wire:model="password_actual" autocomplete="current-password" class="w-full bg-slate-50 border border-slate-200/70 focus:bg-white focus:border-sovereign-blue focus:ring-2 focus:ring-sovereign-blue/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none transition-all placeholder:text-slate-400" placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password_actual')" class="mt-2 text-xs" />
                    </div>
                </div>

                <div class="w-full h-px bg-slate-100 my-2"></div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                    <label class="w-48 flex-shrink-0 text-[13px] font-semibold text-slate-700">Nueva contraseña</label>
                    <div class="flex-1">
                        <input type="password" wire:model="password_nueva" autocomplete="new-password" class="w-full bg-slate-50 border border-slate-200/70 focus:bg-white focus:border-sovereign-blue focus:ring-2 focus:ring-sovereign-blue/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none transition-all placeholder:text-slate-400" placeholder="Mínimo 8 caracteres">
                        <x-input-error :messages="$errors->get('password_nueva')" class="mt-2 text-xs" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                    <label class="w-48 flex-shrink-0 text-[13px] font-semibold text-slate-700">Confirmar contraseña</label>
                    <div class="flex-1">
                        <input type="password" wire:model="password_nueva_confirmation" autocomplete="new-password" class="w-full bg-slate-50 border border-slate-200/70 focus:bg-white focus:border-sovereign-blue focus:ring-2 focus:ring-sovereign-blue/20 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none transition-all placeholder:text-slate-400" placeholder="Repite la contraseña">
                        <x-input-error :messages="$errors->get('password_nueva_confirmation')" class="mt-2 text-xs" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end px-6 sm:px-8 py-4 bg-slate-50/50 border-t border-slate-100">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm active:scale-[0.98]">
                    Actualizar contraseña
                </button>
            </div>
        </form>
    </div>

    <!-- Zona de peligro -->
    <div class="bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden">
        <div class="px-6 sm:px-8 py-5 border-b border-red-100 bg-red-50/30">
            <h3 class="text-base font-bold text-red-700">Zona de peligro</h3>
            <p class="text-[13px] text-red-600/80 mt-1">Al eliminar tu cuenta se borrarán permanentemente todos tus datos.</p>
        </div>

        <div class="px-6 sm:px-8 py-5 flex items-center justify-between bg-white">
            <div class="text-sm text-slate-600 max-w-lg hidden sm:block">
                Una vez eliminada la cuenta, no habrá forma de recuperar tu acceso, facturas ni configuraciones.
            </div>
            <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirmar-eliminacion')"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-red-50 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-600 hover:text-white transition-all active:scale-[0.98] w-full sm:w-auto border border-red-100 hover:border-red-600">
                Eliminar mi cuenta
            </button>
        </div>
    </div>

    <!-- Modal confirmar eliminación -->
    <x-modal name="confirmar-eliminacion" maxWidth="md" focusable>
        <form wire:submit="eliminarCuenta" class="p-6 sm:p-8">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-red-600 text-[28px]">warning</span>
                </div>
                <div class="pt-1">
                    <h2 class="text-lg font-bold text-slate-900">Eliminar cuenta</h2>
                    <p class="mt-1.5 text-sm text-slate-500 leading-relaxed">Esta acción es irreversible. Por favor, escribe tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.</p>
                </div>
            </div>

            <div class="mt-6">
                <input type="password" wire:model="password_eliminar" placeholder="Tu contraseña actual" autocomplete="current-password"
                    class="w-full bg-slate-50 border border-slate-200/70 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-500/20 rounded-xl px-4 py-3 text-sm text-slate-900 focus:outline-none transition-all placeholder:text-slate-400">
                <x-input-error :messages="$errors->get('password_eliminar')" class="mt-2 text-xs" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 bg-white border border-slate-200 text-sm font-semibold rounded-xl text-slate-700 hover:bg-slate-50 transition-colors shadow-sm active:scale-[0.98]">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-colors shadow-sm active:scale-[0.98]">
                    Sí, eliminar cuenta
                </button>
            </div>
        </form>
    </x-modal>
</div>