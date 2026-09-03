@section('breadcrumbs')
    <x-breadcrumbs :links="[
        ['title' => 'Configuración', 'url' => null],
        ['title' => 'Empresa', 'url' => null],
    ]" />
@endsection

<div>
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Empresa y configuración general</h2>
            <p class="text-slate-500 text-sm mt-1">Administra los datos fiscales, marca comercial y preferencias del PDF.</p>
        </div>
        <button wire:click="guardar" type="button" class="inline-flex items-center justify-center px-4 py-2 bg-sovereign-blue text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors shadow-sm whitespace-nowrap">
            <span class="material-symbols-outlined text-[20px] mr-2">save</span>
            Guardar cambios
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Left Navigation (Tabs) -->
        <!-- Internal Nav -->
        <div class="md:col-span-3">
            <div class="sticky top-24">
                <nav class="flex flex-row overflow-x-auto md:flex-col gap-1 pb-2 md:pb-0 scrollbar-hide">
                    <button wire:click="$set('tabActiva', 'general')" type="button"
                        class="text-left py-2 px-4 rounded-lg text-sm font-medium transition-all whitespace-nowrap border-b-2 md:border-b-0 md:border-l-2 {{ $tabActiva === 'general' ? 'bg-slate-100 text-slate-900 border-sovereign-blue' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 border-transparent' }}">
                        General
                    </button>
                    <button wire:click="$set('tabActiva', 'impuestos')" type="button"
                        class="text-left py-2 px-4 rounded-lg text-sm font-medium transition-all whitespace-nowrap border-b-2 md:border-b-0 md:border-l-2 {{ $tabActiva === 'impuestos' ? 'bg-slate-100 text-slate-900 border-sovereign-blue' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 border-transparent' }}">
                        Impuesto y PDF
                    </button>
                </nav>
            </div>
        </div>

        <!-- Forms Content -->
        <div class="md:col-span-9 flex flex-col gap-12 pb-12">
            
            @if($tabActiva === 'general')
            <!-- Tab: General (Marca + Moneda) -->
            <div class="animate-fade-in-up flex flex-col gap-8">
                <section>
                    <h3 class="text-base font-semibold text-slate-700 mb-4">Identidad de marca</h3>
                    <div class="bg-white rounded-lg border border-slate-200 p-6 grid grid-cols-1 md:grid-cols-2 gap-8 shadow-sm">
                        <!-- Logo Upload -->
                        <div class="flex flex-col gap-4">
                            <label class="text-xs font-bold uppercase tracking-wider text-gray-500">Logotipo de la empresa</label>
                            
                            <div x-data="{ isUploading: false, progress: 0 }" 
                                 x-on:livewire-upload-start="isUploading = true"
                                 x-on:livewire-upload-finish="isUploading = false"
                                 x-on:livewire-upload-error="isUploading = false"
                                 x-on:livewire-upload-progress="progress = $event.detail.progress"
                                 class="relative border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center p-8 bg-gray-50 text-center hover:bg-gray-100 transition-colors cursor-pointer group">
                                
                                <input type="file" wire:model="logo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/png, image/jpeg, image/webp" />
                                
                                @if($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-full mb-4 border border-gray-300 shadow-sm" alt="Preview">
                                @elseif($logo_path_actual)
                                    <img src="{{ Storage::url($logo_path_actual) }}" class="w-20 h-20 object-cover rounded-full mb-4 border border-gray-300 shadow-sm" alt="Logo actual">
                                @else
                                    <div class="w-20 h-20 rounded-full bg-white border border-gray-200 flex items-center justify-center mb-4 overflow-hidden group-hover:border-[#1A2B44] transition-colors">
                                        <span class="material-symbols-outlined text-gray-400 text-3xl group-hover:text-[#1A2B44] transition-colors">image</span>
                                    </div>
                                @endif
                                
                                <span class="text-sm font-medium text-gray-900">Arrastra tu logo aquí</span>
                                <span class="text-sm text-gray-500 mt-1">o haz clic para explorar. PNG, JPG o WEBP.</span>
                                
                                <!-- Progress Bar -->
                                <div x-show="isUploading" class="w-full mt-4">
                                    <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-[#1A2B44] h-2 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                                    </div>
                                </div>
                            </div>
                            @error('logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Brand Inputs -->
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-gray-500 flex gap-1">Nombre comercial <span class="text-red-500">*</span></label>
                                <input wire:model="nombre" class="bg-gray-50 border border-gray-300 rounded px-4 py-2 text-sm text-gray-900 focus:outline-none focus:border-[#1A2B44] focus:ring-1 focus:ring-[#1A2B44] transition-all" type="text" placeholder="Ej. Acme Corp"/>
                                @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-gray-500">Identificación fiscal (Opcional)</label>
                                <input wire:model="identificacion_fiscal" class="bg-gray-50 border border-gray-300 rounded px-4 py-2 text-sm text-gray-900 focus:outline-none focus:border-[#1A2B44] focus:ring-1 focus:ring-[#1A2B44] transition-all" placeholder="Ej. RUC, CUIT, NIF..." type="text"/>
                                @error('identificacion_fiscal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-base font-semibold text-slate-700 mb-4">Moneda</h3>
                    <div class="bg-white rounded-lg border border-slate-200 p-6 flex flex-col md:flex-row gap-6 items-start md:items-center justify-between shadow-sm">
                        <div class="flex gap-4 w-full md:w-auto">
                            <div class="flex flex-col gap-2 flex-1 md:w-48">
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Código</label>
                                <div class="relative">
                                    <select wire:model="moneda" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-2 text-sm text-slate-900 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue transition-all appearance-none pr-10">
                                        <option value="USD - Dólar">USD - Dólar</option>
                                    </select>
                                </div>
                                @error('moneda') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col gap-2 w-24">
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Símbolo</label>
                                <input wire:model.live="simbolo_moneda" class="bg-slate-50 border border-slate-300 rounded-lg px-4 py-2 text-sm text-slate-900 text-center focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue transition-all" type="text" placeholder="$"/>
                                @error('simbolo_moneda') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2 bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Vista previa</span>
                            <span class="text-xl font-bold text-sovereign-blue bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200">{{ $simbolo_moneda }}1,250.00</span>
                        </div>
                    </div>
                </section>
            </div>
            @endif



            @if($tabActiva === 'impuestos')
            <!-- Tab: Impuesto y Apariencia PDF -->
            <div class="animate-fade-in-up">
                <section>
                    <h3 class="text-base font-semibold text-slate-700 mb-4">Impuesto y Apariencia PDF</h3>
                    <div class="bg-white rounded-lg border border-slate-200 p-6 grid grid-cols-1 md:grid-cols-2 gap-8 shadow-sm">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col gap-4">
                                <h4 class="text-sm font-semibold text-slate-700 border-b border-slate-200 pb-2">Impuesto Predeterminado</h4>
                                <div class="flex gap-4">
                                    <div class="flex flex-col gap-1.5 flex-1">
                                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Nombre del Impuesto</label>
                                        <input wire:model.live="impuesto_nombre" class="bg-slate-50 border border-slate-300 rounded-lg px-4 py-2 text-sm text-slate-900 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue transition-all" type="text" placeholder="ITBMS"/>
                                        @error('impuesto_nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex flex-col gap-1.5 w-24">
                                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">%</label>
                                        <input wire:model.live="impuesto_porcentaje" class="bg-slate-50 border border-slate-300 rounded-lg px-4 py-2 text-sm text-slate-900 text-center focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue transition-all" type="number" step="0.01"/>
                                        @error('impuesto_porcentaje') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-slate-500">Aparecerá como:</span>
                                    <span class="bg-blue-50 text-sovereign-blue px-3 py-1 rounded-full text-sm font-medium border border-blue-200">{{ $this->impuesto_chip_preview }}</span>
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-4">
                                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Notas al Pie de Factura</h3>
                                <label class="text-xs font-bold uppercase tracking-wider text-gray-500">Términos, condiciones o notas de agradecimiento</label>
                                <textarea wire:model="pie_pagina_pdf" class="bg-gray-50 border border-gray-300 rounded px-4 py-3 text-sm text-gray-900 focus:outline-none focus:border-[#1A2B44] focus:ring-1 focus:ring-[#1A2B44] transition-all min-h-[100px] resize-y" placeholder="Ej. Gracias por su negocio. El pago vence en 30 días."></textarea>
                                @error('pie_pagina_pdf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-6">
                            <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Apariencia del PDF</h3>
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-gray-500">Color de Acento</label>
                                <div class="flex gap-3 items-center">
                                    <button wire:click="$set('color_primario', '#1A2B44')" type="button" class="w-8 h-8 rounded-full bg-[#1A2B44] border-2 {{ $color_primario === '#1A2B44' ? 'border-white ring-2 ring-[#1A2B44] ring-offset-2' : 'border-gray-300 hover:scale-110' }} transition-all"></button>
                                    <button wire:click="$set('color_primario', '#2D3136')" type="button" class="w-8 h-8 rounded-full bg-[#2D3136] border-2 {{ $color_primario === '#2D3136' ? 'border-white ring-2 ring-[#2D3136] ring-offset-2' : 'border-gray-300 hover:scale-110' }} transition-all"></button>
                                    <button wire:click="$set('color_primario', '#4e5f78')" type="button" class="w-8 h-8 rounded-full bg-[#4e5f78] border-2 {{ $color_primario === '#4e5f78' ? 'border-white ring-2 ring-[#4e5f78] ring-offset-2' : 'border-gray-300 hover:scale-110' }} transition-all"></button>
                                    <button wire:click="$set('color_primario', '#04162e')" type="button" class="w-8 h-8 rounded-full bg-[#04162e] border-2 {{ $color_primario === '#04162e' ? 'border-white ring-2 ring-[#04162e] ring-offset-2' : 'border-gray-300 hover:scale-110' }} transition-all"></button>
                                    <div class="relative flex items-center">
                                        <input type="color" wire:model.live="color_primario" class="w-8 h-8 rounded cursor-pointer border border-gray-300 overflow-hidden outline-none p-0 bg-transparent" />
                                    </div>
                                </div>
                                @error('color_primario') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mt-4 bg-gray-50 rounded border border-gray-200 p-4 shadow-inner flex flex-col items-center">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-4 self-start">Mockup de Cabecera PDF</span>
                                <div class="w-full bg-white border border-gray-200 shadow-sm rounded flex flex-col overflow-hidden">
                                    <div class="h-4 w-full transition-colors" style="background-color: {{ $color_primario ?? '#1A2B44' }}"></div>
                                    <div class="p-4 flex justify-between items-start">
                                        @if($logo)
                                            <img src="{{ $logo->temporaryUrl() }}" class="w-12 h-12 object-contain" alt="Logo">
                                        @elseif($logo_path_actual)
                                            <img src="{{ Storage::url($logo_path_actual) }}" class="w-12 h-12 object-contain" alt="Logo">
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded border border-gray-200 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-gray-400 text-xl">image</span>
                                            </div>
                                        @endif
                                        <div class="text-right">
                                            <div class="font-bold text-sm transition-colors" style="color: {{ $color_primario ?? '#1A2B44' }}">FACTURA</div>
                                            <div class="text-xs text-gray-500 mt-1">FAC-000100</div>
                                        </div>
                                    </div>
                                    <div class="px-4 pb-4 flex justify-between text-[10px] text-gray-500">
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $nombre ?: 'Nombre de la empresa' }}</div>
                                            <div>{{ $identificacion_fiscal ? 'ID: ' . $identificacion_fiscal : '' }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div>Fecha: {{ date('d/m/Y') }}</div>
                                            <div>Monto: {{ $simbolo_moneda }}1,250.00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            @endif

        </div>
    </div>
    
    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.3s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</div>
