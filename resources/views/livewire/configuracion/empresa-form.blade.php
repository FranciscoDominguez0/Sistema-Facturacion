@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => route('settings.empresa')],
        ['title' => 'Detalles de la Empresa', 'url' => null],
    ]" />
@endsection

<x-settings-layout activa="empresa">
    <!-- Sub-tabs -->
    <div x-data="{ tab: 'detalles' }" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-6 py-5 flex items-center justify-between bg-white border-b border-slate-100">
            <h2 class="text-xl text-slate-900 font-bold">Empresa</h2>
            <button wire:click="guardar" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors shadow-sm whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Guardar cambios
            </button>
        </div>

        <!-- Tab Nav -->
        <div class="flex border-b border-slate-200 overflow-x-auto">
            <button @click="tab = 'detalles'" :class="tab === 'detalles' ? 'border-sovereign-blue text-sovereign-blue font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" class="flex-shrink-0 px-5 py-3.5 text-sm border-b-2 transition-all -mb-px">
                Detalles
            </button>
            <button @click="tab = 'logo'" :class="tab === 'logo' ? 'border-sovereign-blue text-sovereign-blue font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" class="flex-shrink-0 px-5 py-3.5 text-sm border-b-2 transition-all -mb-px">
                Logo
            </button>
            <button @click="tab = 'valores'" :class="tab === 'valores' ? 'border-sovereign-blue text-sovereign-blue font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'" class="flex-shrink-0 px-5 py-3.5 text-sm border-b-2 transition-all -mb-px">
                Valores por Defecto
            </button>
        </div>

        <!-- Tab: Detalles -->
        <div x-show="tab === 'detalles'" x-cloak class="divide-y divide-slate-100">

            <div class="flex items-center gap-6 px-6 py-4">
                <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Nombre de Empresa <span class="text-red-500">*</span></label>
                <div class="flex-1">
                    <input wire:model="form.nombre" type="text" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                    @error('form.nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center gap-6 px-6 py-4">
                <label class="w-48 flex-shrink-0 text-sm text-slate-600 font-medium">Identificación Fiscal</label>
                <div class="flex-1">
                    <input wire:model="form.identificacion_fiscal" type="text" placeholder="RUC, NIF, CUIT..." class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                    @error('form.identificacion_fiscal') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

        </div>

        <!-- Tab: Logo -->
        <div x-show="tab === 'logo'" x-cloak class="p-6">

            <div x-data="{ isUploading: false, progress: 0 }"
                 x-on:livewire-upload-start="isUploading = true"
                 x-on:livewire-upload-finish="isUploading = false"
                 x-on:livewire-upload-error="isUploading = false"
                 x-on:livewire-upload-progress="progress = $event.detail.progress"
                 class="flex flex-col sm:flex-row gap-8 items-start">

                <!-- Preview actual -->
                <div class="flex-shrink-0 flex flex-col items-center gap-3">
                    <div class="w-28 h-28 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden">
                        @if($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="w-full h-full object-contain" alt="Preview">
                        @elseif($logo_path_actual)
                            <img src="{{ Storage::url($logo_path_actual) }}" class="w-full h-full object-contain" alt="Logo actual">
                        @else
                            <span class="material-symbols-outlined text-slate-300 text-4xl">image</span>
                        @endif
                    </div>
                    <span class="text-xs text-slate-400">Logo actual</span>
                </div>

                <!-- Upload area -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-700 mb-3">Subir nuevo logo</label>
                    <div class="relative border-2 border-dashed border-slate-200 rounded-xl flex flex-col items-center justify-center p-8 bg-slate-50 text-center hover:bg-slate-100 hover:border-sovereign-blue/40 transition-all cursor-pointer group">
                        <input type="file" wire:model="logo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/png, image/jpeg, image/webp">
                        <span class="material-symbols-outlined text-slate-300 group-hover:text-sovereign-blue text-4xl mb-2 transition-colors">upload</span>
                        <p class="text-sm font-medium text-slate-700">Arrastra o haz clic para explorar</p>
                        <p class="text-xs text-slate-400 mt-1">PNG, JPG o WEBP · Máx. 2 MB</p>

                        <!-- Progress Bar -->
                        <div x-show="isUploading" class="w-full mt-4">
                            <div class="bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-sovereign-blue h-1.5 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                            </div>
                            <p class="text-xs text-slate-400 mt-1 text-center" x-text="`Subiendo... ${progress}%`"></p>
                        </div>
                    </div>
                    @error('logo') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                </div>
            </div>

        </div>

        <!-- Tab: Valores por Defecto -->
        <div x-show="tab === 'valores'" x-cloak class="divide-y divide-slate-100">

            <!-- Moneda -->
            <div class="px-6 py-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Moneda</h3>
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center gap-6 flex-1 min-w-0">
                        <label class="w-32 flex-shrink-0 text-sm text-slate-600 font-medium">Código</label>
                        <div class="flex-1">
                            <select wire:model="form.moneda" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                                <option value="USD - Dólar">USD - Dólar</option>
                                <option value="EUR - Euro">EUR - Euro</option>
                                <option value="COP - Peso Colombiano">COP - Peso Colombiano</option>
                                <option value="MXN - Peso Mexicano">MXN - Peso Mexicano</option>
                                <option value="PEN - Sol Peruano">PEN - Sol Peruano</option>
                                <option value="ARS - Peso Argentino">ARS - Peso Argentino</option>
                            </select>
                            @error('form.moneda') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="w-16 flex-shrink-0 text-sm text-slate-600 font-medium">Símbolo</label>
                        <div class="w-20">
                            <input wire:model.live="form.simbolo_moneda" type="text" placeholder="$" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 text-center focus:outline-none transition-all">
                            @error('form.simbolo_moneda') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-2">
                        <span class="text-xs text-slate-500">Vista previa:</span>
                        <span class="text-base font-bold text-sovereign-blue">{{ $form->simbolo_moneda }}1,250.00</span>
                    </div>
                </div>
            </div>

            <!-- Impuesto -->
            <div class="px-6 py-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Impuesto predeterminado</h3>
                <div class="flex flex-wrap gap-6 items-end">
                    <div class="flex items-center gap-6 flex-1 min-w-0">
                        <label class="w-32 flex-shrink-0 text-sm text-slate-600 font-medium">Nombre</label>
                        <div class="flex-1">
                            <input wire:model.live="form.impuesto_nombre" type="text" placeholder="ITBMS, IVA..." class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all">
                            @error('form.impuesto_nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="w-16 flex-shrink-0 text-sm text-slate-600 font-medium">Porcentaje</label>
                        <div class="w-24">
                            <input wire:model.live="form.impuesto_porcentaje" type="number" step="0.01" min="0" max="100" class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 text-center focus:outline-none transition-all">
                            @error('form.impuesto_porcentaje') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <span class="inline-flex items-center bg-blue-50 text-sovereign-blue text-sm font-medium px-3 py-1.5 rounded-full border border-blue-200">
                        {{ $this->impuesto_chip_preview }}
                    </span>
                </div>
            </div>

            <!-- Pie de página PDF -->
            <div class="px-6 py-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Notas al pie de factura</h3>
                <div class="flex gap-6">
                    <label class="w-32 flex-shrink-0 text-sm text-slate-600 font-medium pt-2">Pie de página</label>
                    <div class="flex-1">
                        <textarea wire:model="form.pie_pagina_pdf" rows="3" placeholder="Ej. Gracias por su negocio. El pago vence en 30 días." class="w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none transition-all resize-none"></textarea>
                        @error('form.pie_pagina_pdf') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

        </div>

    </div>

    <style>[x-cloak] { display: none !important; }</style>
</x-settings-layout>
