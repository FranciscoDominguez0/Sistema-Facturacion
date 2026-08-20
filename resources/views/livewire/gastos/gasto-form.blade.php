<div class="max-w-3xl mx-auto w-full">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Gastos', 'url' => route('gastos')],
            ['title' => 'Nuevo Gasto']
        ]" />
    @endsection

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Nuevo Gasto</h2>
        <p class="text-slate-500 text-sm mt-1">
            Registra un egreso del negocio.
        </p>
    </div>

    <form wire:submit="guardar" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 space-y-6">
            <div>
                <label for="concepto" class="block text-sm font-semibold text-slate-700 mb-1">Concepto <span class="text-red-500">*</span></label>
                <input type="text" id="concepto" wire:model="form.concepto" class="w-full bg-white border @error('form.concepto') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors">
                @error('form.concepto') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="categoria" class="block text-sm font-semibold text-slate-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select id="categoria" wire:model="form.categoria" class="w-full appearance-none bg-white bg-none border @error('form.categoria') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg pl-4 pr-10 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors cursor-pointer">
                        <option value="">Selecciona una categoría...</option>
                        @foreach(config('gastos.categorias') as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                </div>
                @error('form.categoria') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="monto" class="block text-sm font-semibold text-slate-700 mb-1">Monto <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0.01" id="monto" wire:model="form.monto" class="w-full bg-white border @error('form.monto') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg pl-4 pr-8 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors" placeholder="0.00">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">$</span>
                    </div>
                    @error('form.monto') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-semibold text-slate-700 mb-1">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" id="fecha" wire:model="form.fecha" class="w-full bg-white border @error('form.fecha') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors">
                    @error('form.fecha') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label for="comprobante" class="block text-sm font-semibold text-slate-700 mb-1">Comprobante</label>
                <input type="text" id="comprobante" wire:model="form.comprobante" class="w-full bg-white border @error('form.comprobante') border-red-300 focus:border-red-500 focus:ring-red-500 @else border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue @enderror rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors" placeholder="N° de comprobante / referencia">
                @error('form.comprobante') <span class="text-red-500 text-xs mt-1 block font-medium flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200">
            <a href="{{ route('gastos') }}" wire:navigate class="px-5 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 bg-sovereign-blue text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition-colors shadow-sm flex items-center gap-2" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="guardar" class="material-symbols-outlined text-[18px]">save</span>
                <span wire:loading wire:target="guardar" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                Guardar Gasto
            </button>
        </div>
    </form>
</div>
