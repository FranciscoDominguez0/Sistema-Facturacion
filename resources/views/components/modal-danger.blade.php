@props([
    'show' => 'false',
    'title' => '',
    'maxWidth' => 'md'
])

@php
$maxWidthClass = match ($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    default => 'max-w-md',
};
@endphp

<div x-data="{ show: @entangle($show) }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        
        <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" @click="show = false"></div>

        <div x-show="show" x-transition class="relative inline-block w-full {{ $maxWidthClass }} p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-red-100">
            <div class="flex items-start justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-red-100 text-red-600">
                        <span class="material-symbols-outlined">warning</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">
                        {{ $title }}
                    </h3>
                </div>
                <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-500 rounded-lg p-1 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            
            <div class="mt-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
