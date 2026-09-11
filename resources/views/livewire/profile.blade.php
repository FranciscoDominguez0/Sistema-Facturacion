@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Perfil', 'url' => null],
    ]" />
@endsection

<div class="max-w-4xl space-y-6">
    <!-- Tarjeta de presentación -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-28 bg-gradient-to-r from-sovereign-blue to-slate-800"></div>
        <div class="px-6 pb-6 -mt-14">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <!-- Foto de perfil: clic para cambiar, guarda automáticamente -->
                <div class="relative flex-shrink-0 group" x-data="{}">
                    <div class="w-28 h-28 rounded-full border-4 border-white bg-gradient-to-br from-slate-100 to-slate-200 shadow-lg overflow-hidden flex items-center justify-center">
                        @if ($avatar_path_actual)
                            <img src="{{ Storage::url($avatar_path_actual) }}" class="w-full h-full object-cover" alt="Foto de perfil">
                        @else
                            <span class="material-symbols-outlined text-5xl text-slate-300">person</span>
                        @endif
                    </div>

                    <input type="file" x-ref="inputAvatar" wire:model="avatar" accept="image/png, image/jpeg, image/webp" class="hidden">
                    <button type="button" @click="$refs.inputAvatar.click()" title="Cambiar foto"
                        class="absolute inset-0 rounded-full bg-sovereign-blue/70 opacity-0 group-hover:opacity-100 transition-all duration-200 flex flex-col items-center justify-center text-white cursor-pointer">
                        <span class="material-symbols-outlined text-2xl">photo_camera</span>
                        <span class="text-[11px] font-semibold mt-0.5">Cambiar foto</span>
                    </button>

                    @if ($avatar_path_actual)
                        <button type="button" wire:click="eliminarAvatar" title="Quitar foto"
                            class="absolute -bottom-0.5 -right-0.5 w-8 h-8 rounded-full bg-white text-red-500 flex items-center justify-center shadow-md border border-slate-200 hover:bg-red-500 hover:text-white hover:scale-105 active:scale-95 transition-all duration-200">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    @endif
                </div>

                <div class="pb-1 min-w-0">
                    <h2 class="text-xl font-bold text-slate-900 truncate">{{ $nombre }}</h2>
                    <p class="text-sm text-slate-500 truncate">{{ $email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Información personal -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">Información personal</h3>
            <p class="text-sm text-slate-500 mt-0.5">Actualiza tus datos de contacto.</p>
        </div>

        <form wire:submit="actualizarPerfil" class="divide-y divide-slate-100">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 px-6 py-4">
                <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Nombre completo <span class="text-red-500">*</span></label>
                <div class="flex-1">
                    <input type="text" wire:model="nombre" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2 text-xs" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 px-6 py-4">
                <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Correo electrónico <span class="text-red-500">*</span></label>
                <div class="flex-1">
                    <input type="email" wire:model="email" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
                </div>
            </div>

            <div class="flex justify-end px-6 py-4 bg-slate-50/50">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    <!-- Contraseña -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800">Contraseña</h3>
            <p class="text-sm text-slate-500 mt-0.5">Usa una contraseña larga y aleatoria para mantener tu cuenta segura.</p>
        </div>

        <form wire:submit="actualizarPassword" class="divide-y divide-slate-100">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 px-6 py-4">
                <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Contraseña actual</label>
                <div class="flex-1">
                    <input type="password" wire:model="password_actual" autocomplete="current-password" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                    <x-input-error :messages="$errors->get('password_actual')" class="mt-2 text-xs" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 px-6 py-4">
                <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Nueva contraseña</label>
                <div class="flex-1">
                    <input type="password" wire:model="password_nueva" autocomplete="new-password" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                    <x-input-error :messages="$errors->get('password_nueva')" class="mt-2 text-xs" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 px-6 py-4">
                <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Confirmar contraseña</label>
                <div class="flex-1">
                    <input type="password" wire:model="password_nueva_confirmation" autocomplete="new-password" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                    <x-input-error :messages="$errors->get('password_nueva_confirmation')" class="mt-2 text-xs" />
                </div>
            </div>

            <div class="flex justify-end px-6 py-4 bg-slate-50/50">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">lock</span>
                    Actualizar contraseña
                </button>
            </div>
        </form>
    </div>

    <!-- Zona de peligro -->
    <div class="bg-white rounded-xl border border-red-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-red-100 bg-red-50/40">
            <h3 class="text-base font-bold text-red-700">Eliminar cuenta</h3>
            <p class="text-sm text-slate-500 mt-0.5">Al eliminar tu cuenta se borrarán permanentemente todos tus datos. Esta acción es irreversible.</p>
        </div>

        <div class="px-6 py-4 flex justify-end">
            <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirmar-eliminacion')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[18px]">person_remove</span>
                Eliminar mi cuenta
            </button>
        </div>
    </div>

    <!-- Modal confirmar eliminación -->
    <x-modal name="confirmar-eliminacion" maxWidth="md" focusable>
        <form wire:submit="eliminarCuenta" class="p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-red-600">warning</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">¿Seguro que quieres eliminar tu cuenta?</h2>
                    <p class="mt-1 text-sm text-slate-500">Una vez eliminada, todos tus recursos y datos se perderán de forma permanente. Escribe tu contraseña para confirmar.</p>
                </div>
            </div>

            <div class="mt-6">
                <input type="password" wire:model="password_eliminar" placeholder="Contraseña" autocomplete="current-password"
                    class="w-full bg-white border border-slate-200 focus:border-red-500 focus:ring-1 focus:ring-red-500 rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                <x-input-error :messages="$errors->get('password_eliminar')" class="mt-2 text-xs" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 bg-white border border-slate-200 text-sm font-semibold rounded-lg text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                    Eliminar definitivamente
                </button>
            </div>
        </form>
    </x-modal>
</div>