<div class="w-full">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Clientes', 'url' => route('clientes')],
            ['title' => $form->cliente ? 'Editar Cliente' : 'Nuevo Cliente']
        ]" />
    @endsection

    <!-- Modal/Form Container -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="px-8 py-6 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    {{ $form->cliente ? 'Editar Cliente' : 'Nuevo Cliente' }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $form->cliente ? 'Actualice los datos del cliente.' : 'Ingrese los detalles para registrar un nuevo cliente en el sistema.' }}
                </p>
            </div>
        </div>

        <!-- Form Body -->
        <form wire:submit="save">
            <div class="p-8 flex flex-col gap-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Basic Info Group -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold tracking-wider text-sovereign-blue uppercase flex items-center gap-1">
                            Nombre Completo <span class="text-red-600 text-sm">*</span>
                        </label>
                        <div class="bg-white border @error('form.nombre') border-red-500 @else border-slate-200 @enderror rounded-lg shadow-sm px-4 py-3 focus-within:border-sovereign-blue focus-within:ring-1 focus-within:ring-sovereign-blue transition-all flex items-center">
                            <input wire:model="form.nombre" class="bg-transparent border-none outline-none w-full text-sm text-slate-800 p-0 focus:ring-0" type="text"/>
                        </div>
                        @error('form.nombre')
                            <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold tracking-wider text-sovereign-blue uppercase">
                            Identificación Fiscal (Opcional)
                        </label>
                        <div class="bg-white border @error('form.identificacion') border-red-500 @else border-slate-200 @enderror rounded-lg shadow-sm px-4 py-3 focus-within:border-sovereign-blue focus-within:ring-1 focus-within:ring-sovereign-blue transition-all flex items-center">
                            <input wire:model="form.identificacion" class="bg-transparent border-none outline-none w-full text-sm text-slate-800 p-0 focus:ring-0" type="text"/>
                        </div>
                        @error('form.identificacion')
                            <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Contact Group -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold tracking-wider text-sovereign-blue uppercase">
                            Correo Electrónico
                        </label>
                        <div class="bg-white border @error('form.email') border-red-500 @else border-slate-200 @enderror rounded-lg shadow-sm px-4 py-3 focus-within:border-sovereign-blue focus-within:ring-1 focus-within:ring-sovereign-blue transition-all flex items-center">
                            <input wire:model="form.email" class="bg-transparent border-none outline-none w-full text-sm text-slate-800 p-0 focus:ring-0" type="email"/>
                        </div>
                        @error('form.email')
                            <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold tracking-wider text-sovereign-blue uppercase">
                            Teléfono
                        </label>
                        <div class="bg-white border @error('form.telefono') border-red-500 @else border-slate-200 @enderror rounded-lg shadow-sm px-4 py-3 focus-within:border-sovereign-blue focus-within:ring-1 focus-within:ring-sovereign-blue transition-all flex items-center">
                            <input wire:model="form.telefono" class="bg-transparent border-none outline-none w-full text-sm text-slate-800 p-0 focus:ring-0" type="tel"/>
                        </div>
                        @error('form.telefono')
                            <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr class="border-t border-slate-200"/>

                <!-- Location Group -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold tracking-wider text-sovereign-blue uppercase">
                        Dirección Principal
                    </label>
                    <div class="bg-white border @error('form.direccion') border-red-500 @else border-slate-200 @enderror rounded-lg shadow-sm px-4 py-3 focus-within:border-sovereign-blue focus-within:ring-1 focus-within:ring-sovereign-blue transition-all flex items-start">
                        <textarea wire:model="form.direccion" class="bg-transparent border-none outline-none w-full text-sm text-slate-800 p-0 resize-none focus:ring-0" rows="3"></textarea>
                    </div>
                    @error('form.direccion')
                        <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <hr class="border-t border-slate-200"/>

                <!-- Status Toggle -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-slate-50 p-6 rounded-lg border border-slate-200 gap-4">
                    <div class="flex flex-col">
                        <span class="text-base font-bold text-slate-800">Estado del Cliente</span>
                        <span class="text-sm text-slate-500 mt-1">Determina si el cliente puede realizar transacciones en el sistema.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input wire:model="form.activo" class="sr-only peer" type="checkbox"/>
                        <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-sovereign-blue"></div>
                        <span class="ml-3 text-sm font-bold text-slate-700 w-16" x-text="$wire.form.activo ? 'Activo' : 'Inactivo'"></span>
                    </label>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-8 py-6 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-4">
                <a href="{{ route('clientes') }}" wire:navigate class="px-6 py-2 text-sm font-semibold text-sovereign-blue border border-sovereign-blue rounded hover:bg-slate-100 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-semibold bg-sovereign-blue text-white border border-sovereign-blue rounded hover:bg-opacity-90 transition-colors shadow-sm">
                    {{ $form->cliente ? 'Actualizar Cliente' : 'Guardar Cliente' }}
                </button>
            </div>
        </form>
    </div>
</div>
