@props(['disabled' => false])

<div x-data="{
        realValue: @entangle($attributes->wire('model')),
        displayValue: '0,00',
        init() {
            this.updateDisplay(this.realValue);
            this.$watch('realValue', val => {
                // Evitamos actualizar la vista mientras el usuario escribe
                // para que los delays de red de Livewire no le borren números (ej. el cero).
                if (document.activeElement !== this.$refs.input) {
                    this.updateDisplay(val);
                }
            });
        },
        updateDisplay(val) {
            if (val === null || val === '' || val === undefined) {
                this.displayValue = '0,00';
                return;
            }
            this.displayValue = new Intl.NumberFormat('es-AR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(val);
        },
        handleInput(e) {
            let raw = e.target.value.replace(/\D/g, '');
            if (raw === '') {
                this.realValue = null;
                this.updateDisplay(0);
            } else {
                let numeric = parseInt(raw, 10);
                this.realValue = numeric / 100;
                this.updateDisplay(this.realValue);
            }
            
            // Forzar siempre el cursor al final después de que Alpine actualiza el DOM
            this.$nextTick(() => {
                this.$refs.input.setSelectionRange(this.$refs.input.value.length, this.$refs.input.value.length);
            });
        }
    }">
    <input 
        x-ref="input"
        type="text" 
        inputmode="numeric"
        {{ $disabled ? 'disabled' : '' }}
        :value="displayValue" 
        @input="handleInput"
        @focus="$nextTick(() => { $el.setSelectionRange($el.value.length, $el.value.length) })"
        @click="$nextTick(() => { $el.setSelectionRange($el.value.length, $el.value.length) })"
        @blur="updateDisplay(realValue)"
        {!! $attributes->whereDoesntStartWith('wire:model')->merge([
            'class' => 'text-right w-full bg-white border border-slate-200 focus:border-sovereign-blue focus:ring-sovereign-blue rounded-lg px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:outline-none focus:ring-1 transition-colors'
        ]) !!}
    >
</div>
