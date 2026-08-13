<main class="w-full max-w-md px-6 mx-auto py-12 fade-in-up">
    <!-- Header / Logo Area (Outside Card) -->
    <div class="flex items-center justify-center gap-3 mb-8">
        <!-- Logo -->
        <div class="h-10 w-10 bg-slate-900 rounded-full flex items-center justify-center text-white shadow-sm">
            <span class="material-symbols-outlined text-2xl">receipt_long</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">
            Sovereign<span class="font-light">Fact</span>
        </h1>
    </div>

    <!-- Login Card -->
    <div class="bg-white border border-slate-200 rounded p-8 w-full shadow-sm">
        
        <h2 class="text-2xl font-normal text-slate-800 mb-6">Iniciar sesión</h2>

        <!-- Error Alert from Livewire -->
        @error('email')
            <div class="mb-5 bg-red-50 text-red-700 p-3 rounded flex items-start gap-2 border border-red-100 text-sm">
                <span class="material-symbols-outlined shrink-0 text-red-500 text-base mt-0.5">error</span>
                <div class="flex-1">
                    <p class="opacity-90">{{ $message }}</p>
                </div>
            </div>
        @enderror

        <form wire:submit="login" class="flex flex-col gap-5">
            
            <!-- Email Field -->
            <div class="flex flex-col gap-1.5">
                <div class="relative flex items-center bg-white border border-slate-300 focus-within:border-sovereign-blue focus-within:ring-1 focus-within:ring-sovereign-blue rounded transition-all @error('email') border-red-500 focus-within:border-red-500 focus-within:ring-red-500 @enderror">
                    <input wire:model="email" id="email" type="email" required autofocus
                           class="w-full bg-transparent border-none py-2.5 px-3 text-slate-900 placeholder-slate-400 focus:ring-0 rounded text-sm"
                           placeholder="Correo electrónico">
                </div>
            </div>

            <!-- Password Field -->
            <div class="flex flex-col gap-1.5 mt-2">
                <div class="flex justify-between items-end mb-1">
                    <label class="block text-sm text-slate-500" for="password">
                        Contraseña
                    </label>
                    <a href="{{ route('password.request') }}" wire:navigate class="text-sm text-sky-600 hover:text-sky-700 transition-colors">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
                <div class="relative flex items-center bg-white border border-slate-300 focus-within:border-sovereign-blue focus-within:ring-1 focus-within:ring-sovereign-blue rounded transition-all @error('password') border-red-500 focus-within:border-red-500 focus-within:ring-red-500 @enderror" x-data="{ show: false }">
                    <input wire:model="password" id="password" x-bind:type="show ? 'text' : 'password'" required
                           class="w-full bg-transparent border-none py-2.5 pl-3 pr-10 text-slate-900 placeholder-slate-400 focus:ring-0 rounded text-sm"
                           placeholder="">
                    <button type="button" @click="show = !show" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none flex items-center justify-center p-1 rounded transition-colors" aria-label="Mostrar u ocultar contraseña">
                        <span class="material-symbols-outlined text-lg" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>
            </div>

            <!-- Remember Me Checkbox -->
            <div class="flex items-center mt-1 hidden">
                <label class="inline-flex items-center cursor-pointer select-none">
                    <input wire:model="remember" id="remember-me" type="checkbox"
                           class="w-4 h-4 rounded border-slate-300 text-sovereign-blue focus:ring-sovereign-blue bg-white transition-colors">
                    <span class="ml-2 text-sm text-slate-500">Recordarme</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" wire:loading.attr="disabled"
                    class="w-full mt-2 bg-sovereign-blue hover:bg-slate-800 active:scale-[0.99] text-white font-medium text-sm py-2.5 px-4 rounded transition-all flex justify-center items-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed">
                <span wire:loading.remove>Iniciar sesión</span>
                <span wire:loading>Iniciando sesión...</span>
            </button>
        </form>
    </div>
</main>
