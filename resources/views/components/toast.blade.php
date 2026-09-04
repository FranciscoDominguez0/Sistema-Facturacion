{{-- Notificación flotante (arriba-centro, 4 segundos). Se coloca una sola vez en el layout.
     Tipos: success (verde), error (rojo), info (azul).
     Disparar desde Livewire: $this->dispatch('toast', message: 'Texto', type: 'success')
     Disparar con flash:      return redirect()->with('success', 'Texto') --}}
<div class="fixed top-8 inset-x-0 lg:left-64 z-50 pointer-events-none flex justify-center px-4">
    <div x-data="{ 
            show: false, 
            message: '', 
            type: 'success',
            init() {
                @if(session()->has('success'))
                    this.showToast('{{ addslashes(session('success')) }}', 'success');
                @elseif(session()->has('error'))
                    this.showToast('{{ addslashes(session('error')) }}', 'error');
                @endif
            },
            showToast(msg, t) {
                this.message = msg;
                this.type = t;
                this.show = true;
                setTimeout(() => this.show = false, 4000);
            }
         }"
         @toast.window="showToast($event.detail.message, $event.detail.type)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
         class="pointer-events-auto bg-white border border-slate-200 shadow-xl rounded-xl p-4 flex items-center gap-4 w-full max-w-sm"
         style="display: none;">
         
         <!-- Icon -->
         <div :class="{
                'text-emerald-600 bg-emerald-50': type === 'success',
                'text-red-600 bg-red-50': type === 'error',
                'text-sovereign-blue bg-blue-50': type === 'info'
              }" 
              class="flex items-center justify-center w-10 h-10 rounded-full flex-shrink-0">
             <span class="material-symbols-outlined text-[24px]" x-text="type === 'success' ? 'check_circle' : (type === 'error' ? 'error' : 'info')"></span>
         </div>
         
         <!-- Content -->
         <div class="flex-1">
             <p class="text-sm font-bold text-slate-800" x-text="type === 'success' ? 'Operación Exitosa' : (type === 'error' ? 'Ocurrió un Error' : 'Información')"></p>
             <p class="text-xs text-slate-500 mt-0.5" x-text="message"></p>
         </div>
         
         <!-- Close Button -->
         <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none flex-shrink-0 p-1 rounded-full hover:bg-slate-50">
             <span class="material-symbols-outlined text-[20px]">close</span>
         </button>
    </div>
</div>
