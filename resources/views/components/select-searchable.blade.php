@props([
    'options' => [], 
    'placeholder' => 'Seleccione...',
    'actionText' => null,
    'actionClick' => null,
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
        if (!this.value) return '';
        const opt = this.options.find(o => o.id == this.value);
        return opt ? opt.nombre : '';
    },
    selectOption(id) {
        this.value = id;
        this.open = false;
        this.search = '';
    }
}" data-options="{{ json_encode($options) }}" @click.outside="open = false; search = ''" class="relative">

    <!-- Seleccionado -->
    <div x-show="value" style="display: none;">
        <div class="flex items-center justify-between bg-slate-50 border border-slate-200 px-4 py-3 rounded-lg text-sm text-slate-800 shadow-sm cursor-pointer" @click="open = true">
            <span class="truncate font-medium text-slate-700" x-text="selectedName"></span>
            <button type="button" @click.stop="value = null; search = ''" class="text-slate-400 hover:text-red-500 transition-colors ml-2 flex-shrink-0">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    </div>

    <!-- No seleccionado / Trigger -->
    <div x-show="!value">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-white border border-slate-200 hover:border-slate-300 rounded-lg px-4 py-3 text-sm text-slate-400 focus:outline-none focus:ring-1 focus:ring-sovereign-blue focus:border-sovereign-blue transition-colors shadow-sm">
            <span x-text="open ? 'Buscar...' : '{{ $placeholder }}'"></span>
            <span class="material-symbols-outlined text-slate-400 text-[20px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
        </button>
    </div>

    <!-- Dropdown -->
    <div x-show="open" x-transition class="absolute z-50 w-full mt-1 bg-white rounded-lg border border-slate-200 shadow-lg overflow-hidden" style="display:none">
        <!-- Input Search -->
        <div class="p-2 border-b border-slate-100">
            <input type="text" x-model="search" x-ref="searchInput" class="w-full bg-slate-50 border border-slate-200 focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue rounded-md px-3 py-2 text-sm text-slate-800 focus:outline-none placeholder-slate-400" placeholder="Escribe para buscar..." autocomplete="off" @click.stop x-init="$watch('open', v => { if(v) setTimeout(() => $refs.searchInput.focus(), 50) })">
        </div>
        
        <!-- Options List -->
        <ul class="max-h-56 overflow-y-auto py-1">
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
        
        <!-- Action Button (New) -->
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
