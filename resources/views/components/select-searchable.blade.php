@props([
    'options' => [],
    'placeholder' => 'Seleccione...',
    'actionText' => null,
    'actionClick' => null,
    'compact' => false,
])

<div x-data="{
    open: false,
    search: '',
    options: [],
    value: @entangle($attributes->wire('model')),
    init() {
        this.options = JSON.parse(this.$el.dataset.options);
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'data-options') {
                    this.options = JSON.parse(this.$el.dataset.options);
                }
            });
        });
        observer.observe(this.$el, { attributes: true });
    },
    get filteredOptions() {
        if (this.search === '') return this.options;
        return this.options.filter(opt => opt.nombre.toLowerCase().includes(this.search.toLowerCase()));
    },
    get selectedName() {
        if (this.value === null || this.value === undefined || this.value === '') return '';
        const opt = this.options.find(o => o.id == this.value);
        if (opt) return opt.nombre;
        // Valor no listado (ej. descuento configurado en el producto): se muestra igual.
        const numero = parseFloat(this.value);
        return isNaN(numero) ? '' : String(parseFloat(numero.toFixed(2))) + '%';
    },
    selectOption(id) {
        this.value = id;
        this.open = false;
        this.search = '';
    },
    manejarIcono() {
        if (this.open) { this.open = false; this.search = ''; return; }
        if (this.value) { this.value = null; this.search = ''; return; }
        this.open = true;
        this.$nextTick(() => this.$refs.searchInput.focus());
    }
}" data-options="{{ json_encode($options) }}" @click.outside="open = false; search = ''" class="relative">

    {{-- Campo único: muestra el valor seleccionado y, al tocarlo, se convierte
         en el buscador (no hay un segundo campo dentro del desplegable). --}}
    <div class="relative">
        <input
            type="text"
            autocomplete="off"
            x-ref="searchInput"
            :value="open ? search : selectedName"
            :placeholder="open ? 'Escribe para buscar...' : '{{ $placeholder }}'"
            @focus="open = true"
            @input="search = $event.target.value"
            @keydown.escape="open = false; search = ''"
            :class="{{ $compact ? "'py-1.5 pl-3 pr-8 rounded-md'" : "'py-3 pl-4 pr-10 rounded-lg'" }}"
            class="w-full bg-white border border-slate-200 hover:border-slate-300 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none transition-colors shadow-sm"
        />
        <button type="button" @click="manejarIcono" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 hover:text-slate-700 transition-colors">
            <span class="material-symbols-outlined text-[20px]" x-text="open || value ? 'close' : 'expand_more'"></span>
        </button>
    </div>

    {{-- Desplegable con las opciones (y la acción al final) --}}
    <div x-show="open" x-transition class="absolute z-50 w-full mt-1 bg-white rounded-lg border border-slate-200 shadow-lg overflow-hidden" style="display:none">
        <ul class="max-h-64 overflow-y-auto py-1">
            <template x-for="opt in filteredOptions" :key="opt.id">
                <li>
                    <button type="button" @click="selectOption(opt.id)" class="w-full text-left px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <span x-text="opt.nombre"></span>
                    </button>
                </li>
            </template>
            <li x-show="filteredOptions.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center">
                Sin resultados.
            </li>
        </ul>

        @if($actionText && $actionClick)
            <div class="border-t border-slate-100">
                <button type="button" @click="{!! $actionClick !!}; open = false;" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-sovereign-blue hover:bg-blue-50 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    {{ $actionText }}
                </button>
            </div>
        @endif
    </div>
</div>