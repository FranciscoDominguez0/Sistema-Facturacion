<main class="w-full max-w-[420px] px-6 mx-auto py-12 fade-in-up">
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

    <!-- Card -->
    <div class="bg-white border border-slate-200 rounded p-8 w-full shadow-sm">
        
        <h2 class="text-2xl font-normal text-slate-800 mb-6">Recuperar contraseña</h2>

        @if ($status)
            <div class="mb-5 bg-sky-50 text-sky-700 p-3 rounded border border-sky-100 text-sm">
                {{ $status }}
            </div>
        @endif

        <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-5">
            <!-- Email Field -->
            <div class="flex flex-col gap-1.5">
                <label class="block text-sm text-slate-500" for="email">
                    Correo electrónico <span class="text-red-500">*</span>
                </label>
                <div class="relative flex items-center bg-white border border-slate-300 focus-within:border-sovereign-blue focus-within:ring-1 focus-within:ring-sovereign-blue rounded transition-all @error('email') border-red-500 focus-within:border-red-500 focus-within:ring-red-500 @enderror">
                    <input wire:model="email" id="email" type="email" required autofocus
                           class="w-full bg-transparent border-none py-2.5 px-3 text-slate-900 placeholder-slate-400 focus:ring-0 rounded text-sm">
                </div>
                @error('email')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Button -->
            <button type="submit" wire:loading.attr="disabled"
                    class="w-full mt-2 bg-sovereign-blue hover:bg-slate-800 active:scale-[0.99] text-white font-medium text-sm py-2.5 px-4 rounded transition-all flex justify-center items-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed">
                <span wire:loading.remove>Enviar instrucciones</span>
                <span wire:loading>Enviando...</span>
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" wire:navigate class="text-sm text-sky-600 hover:text-sky-700 transition-colors">
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</main>
