<div class="max-w-3xl mx-auto w-full">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Productos', 'url' => route('productos.index')],
            ['title' => $isEdit ? 'Editar Producto' : 'Nuevo Producto']
        ]" />
    @endsection

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">{{ $isEdit ? 'Editar Producto' : 'Nuevo Producto' }}</h2>
        <p class="text-slate-500 text-sm mt-1">
            {{ $isEdit ? 'Actualiza la información del producto o servicio.' : 'Agrega un nuevo producto o servicio a tu inventario.' }}
        </p>
    </div>

    <form wire:submit="save" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 space-y-6">
            <!-- Imagen -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Imagen del Producto</label>
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if ($imagen)
                            <img src="{{ $imagen->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif ($isEdit && $producto->imagen_path)
                            <img src="{{ Storage::url($producto->imagen_path) }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-slate-400 text-3xl">image</span>
                        @endif
                    </div>
                    <div>
                        <input type="file" wire:model="imagen" id="imagen" class="hidden" accept="image/*">
                        <label for="imagen" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">upload</span>
                            Seleccionar Imagen
                        </label>
                        <p class="text-xs text-slate-500 mt-2">JPG, PNG, GIF o WEBP. Máximo 2MB.</p>
                        @error('imagen') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="md:col-span-2">
                    <label for="nombre" class="block text-sm font-semibold text-slate-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" id="nombre" wire:model="form.nombre" class="w-full bg-white border @error('form.nombre') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors">
                    @error('form.nombre') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <!-- Código -->
                <div>
                    <label for="codigo" class="block text-sm font-semibold text-slate-700 mb-1">Código / SKU</label>
                    <input type="text" id="codigo" wire:model="form.codigo" class="w-full bg-white border @error('form.codigo') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors">
                    @error('form.codigo') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <!-- Tipo -->
                <div>
                    <label for="tipo" class="block text-sm font-semibold text-slate-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select id="tipo" wire:model="form.tipo" class="w-full appearance-none bg-white bg-none border @error('form.tipo') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg pl-4 pr-10 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors cursor-pointer">
                            <option value="producto">Producto</option>
                            <option value="servicio">Servicio</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>
                    @error('form.tipo') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <!-- Precio -->
                <div>
                    <label for="precio" class="block text-sm font-semibold text-slate-700 mb-1">Precio <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">$</span>
                        <input type="number" step="0.01" id="precio" wire:model="form.precio" class="w-full bg-white border @error('form.precio') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg pl-8 pr-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors" placeholder="0.00">
                    </div>
                    @error('form.precio') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-sm font-semibold text-slate-700 mb-1">Descripción</label>
                    <textarea id="descripcion" wire:model="form.descripcion" rows="3" class="w-full bg-white border @error('form.descripcion') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors resize-none"></textarea>
                    @error('form.descripcion') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Toggles (Aplica Impuesto y Activo) -->
            <div class="pt-4 border-t border-slate-100 flex flex-col md:flex-row gap-6">
                
                <!-- Select Impuesto -->
                <div class="flex-1">
                    <label for="impuesto" class="block text-sm font-semibold text-slate-700 mb-1">Impuesto Aplicable</label>
                    <div class="relative">
                        <select id="impuesto" wire:model="form.impuesto_id" class="w-full appearance-none bg-white border @error('form.impuesto_id') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg pl-4 pr-10 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors cursor-pointer">
                            <option value="">Seleccione un impuesto...</option>
                            @foreach($impuestos as $impuesto)
                                <option value="{{ $impuesto->id }}">{{ $impuesto->nombre }} ({{ number_format($impuesto->porcentaje, 2) }}%)</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>
                    @error('form.impuesto_id') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <!-- Toggle Activo -->
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="$toggle('form.activo')" class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 {{ $form->activo ? 'bg-emerald-500' : 'bg-slate-200' }}" role="switch" aria-checked="{{ $form->activo ? 'true' : 'false' }}">
                        <span aria-hidden="true" class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $form->activo ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                    <div>
                        <span class="block text-sm font-semibold text-slate-700 cursor-pointer" wire:click="$toggle('form.activo')">Estado Activo</span>
                        <span class="block text-xs text-slate-500">¿El ítem está disponible para facturación?</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
            <a href="{{ route('productos.index') }}" wire:navigate class="px-5 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 bg-sovereign-blue text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition-colors shadow-sm flex items-center gap-2" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-[18px]">save</span>
                <span wire:loading wire:target="save" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                {{ $isEdit ? 'Guardar Cambios' : 'Crear Producto' }}
            </button>
        </div>
    </form>
</div>
