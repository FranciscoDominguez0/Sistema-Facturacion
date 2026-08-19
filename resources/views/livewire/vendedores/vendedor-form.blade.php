<div class="max-w-3xl mx-auto w-full">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Vendedores', 'url' => route('vendedores')],
            ['title' => $isEdit ? 'Editar Vendedor' : 'Nuevo Vendedor']
        ]" />
    @endsection

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">{{ $isEdit ? 'Editar Vendedor' : 'Nuevo Vendedor' }}</h2>
        <p class="text-slate-500 text-sm mt-1">
            {{ $isEdit ? 'Actualiza la información del vendedor y sus accesos.' : 'Agrega un nuevo vendedor y configúrale un acceso al sistema.' }}
        </p>
    </div>

    <form wire:submit="save" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 space-y-6">
            
            <div class="border-b border-slate-100 pb-4 mb-4">
                <h3 class="text-lg font-semibold text-slate-800">Datos de Usuario y Acceso</h3>
                <p class="text-xs text-slate-500">Credenciales para iniciar sesión en el sistema.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="md:col-span-2">
                    <label for="nombre" class="block text-sm font-semibold text-slate-700 mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                    <input type="text" id="nombre" wire:model="form.nombre" class="w-full bg-white border @error('form.nombre') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors">
                    @error('form.nombre') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                    <input type="email" id="email" wire:model="form.email" class="w-full bg-white border @error('form.email') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors">
                    @error('form.email') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">
                        Contraseña @if(!$isEdit)<span class="text-red-500">*</span>@endif
                    </label>
                    <input type="password" id="password" wire:model="form.password" class="w-full bg-white border @error('form.password') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors" placeholder="{{ $isEdit ? 'Dejar en blanco para mantener actual' : 'Mínimo 8 caracteres' }}">
                    @error('form.password') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <!-- Rol -->
                <div class="md:col-span-2">
                    <label for="rol" class="block text-sm font-semibold text-slate-700 mb-1">Rol de Sistema <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select id="rol" wire:model="form.rol" class="w-full appearance-none bg-white bg-none border @error('form.rol') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg pl-4 pr-10 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors cursor-pointer">
                            <option value="">Selecciona un rol...</option>
                            @foreach($roles as $roleOption)
                                <option value="{{ $roleOption->name }}">{{ ucfirst($roleOption->name) }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>
                    @error('form.rol') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="border-b border-slate-100 pb-4 mt-8 mb-4">
                <h3 class="text-lg font-semibold text-slate-800">Datos de Ventas</h3>
                <p class="text-xs text-slate-500">Configuración específica para el módulo de facturación.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código -->
                <div>
                    <label for="codigo" class="block text-sm font-semibold text-slate-700 mb-1">Código de Vendedor</label>
                    <input type="text" id="codigo" wire:model="form.codigo" class="w-full bg-white border @error('form.codigo') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors">
                    @error('form.codigo') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <!-- Comisión -->
                <div>
                    <label for="comision_porcentaje" class="block text-sm font-semibold text-slate-700 mb-1">Comisión (%)</label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" id="comision_porcentaje" wire:model="form.comision_porcentaje" class="w-full bg-white border @error('form.comision_porcentaje') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg pl-4 pr-8 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors" placeholder="0">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">%</span>
                    </div>
                    @error('form.comision_porcentaje') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <!-- Descuento Máximo -->
                <div>
                    <label for="descuento_maximo_porcentaje" class="block text-sm font-semibold text-slate-700 mb-1">Dcto. Máximo Autorizado (%)</label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" id="descuento_maximo_porcentaje" wire:model="form.descuento_maximo_porcentaje" class="w-full bg-white border @error('form.descuento_maximo_porcentaje') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg pl-4 pr-8 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors" placeholder="0">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">%</span>
                    </div>
                    @error('form.descuento_maximo_porcentaje') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Toggles (Activo) -->
            <div class="pt-4 border-t border-slate-100 flex flex-col md:flex-row gap-6 mt-4">
                
                <!-- Toggle Activo -->
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="$toggle('form.activo')" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 {{ $form->activo ? 'bg-emerald-500' : 'bg-slate-200' }}" role="switch" aria-checked="{{ $form->activo ? 'true' : 'false' }}">
                        <span aria-hidden="true" class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $form->activo ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                    <div>
                        <span class="block text-sm font-semibold text-slate-700 cursor-pointer" wire:click="$toggle('form.activo')">Estado Activo</span>
                        <span class="block text-xs text-slate-500">¿Puede iniciar sesión y realizar ventas?</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
            @if($isEdit)
                <button type="button" onclick="if(confirm('¿Descartar los cambios?')) { window.location.href='{{ route('vendedores') }}' }" class="px-5 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Cancelar
                </button>
            @else
                <a href="{{ route('vendedores') }}" wire:navigate class="px-5 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Cancelar
                </a>
            @endif
            <button type="submit" class="px-5 py-2 bg-sovereign-blue text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition-colors shadow-sm flex items-center gap-2" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[18px]">save</span>
                <span wire:loading wire:target="save" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                {{ $isEdit ? 'Guardar Cambios' : 'Crear Vendedor' }}
            </button>
        </div>
    </form>
</div>
