@props(['wireProperty', 'onClose' => 'close', 'title' => null, 'maxWidth' => 'lg', 'accent' => 'bg-gradient-to-r from-indigo-400 via-fuchsia-400 to-emerald-400'])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        default => 'sm:max-w-lg',
    };
@endphp

<div x-data="{ open: @entangle($wireProperty).live }" x-cloak>
    <div x-show="open" class="fixed inset-0 z-[70] flex items-end justify-center bg-slate-950/70 p-0 backdrop-blur-sm sm:items-center sm:p-6" role="dialog" aria-modal="true" @if ($title) aria-label="{{ $title }}" @endif>
        <button type="button" class="absolute inset-0 cursor-default" wire:click="{{ $onClose }}" aria-label="Fermer"></button>

        <section
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full opacity-0 sm:translate-y-4"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-full opacity-0 sm:translate-y-4"
            {{ $attributes->merge(['class' => "relative w-full max-w-lg overflow-hidden rounded-t-[2rem] border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 shadow-2xl shadow-slate-950/10 dark:shadow-black/50 {$maxWidthClass} sm:rounded-3xl"]) }}
        >
            <div class="absolute inset-x-0 top-0 h-1 {{ $accent }}"></div>
            <div class="mx-auto mt-3 h-1.5 w-12 rounded-full bg-slate-300 dark:bg-slate-700 sm:hidden"></div>

            @if (isset($header) || $title)
                <div class="flex items-start justify-between gap-4 px-6 pb-5 pt-6 sm:px-8 sm:pt-8">
                    <div class="min-w-0">
                        @if (isset($header))
                            {{ $header }}
                        @else
                            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">{{ $title }}</h2>
                        @endif
                    </div>
                    <button type="button" wire:click="{{ $onClose }}" class="shrink-0 rounded-xl border border-slate-200 dark:border-white/10 p-2 text-slate-500 dark:text-slate-400 transition hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white" aria-label="Fermer">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" /></svg>
                    </button>
                </div>
            @endif

            <div class="px-6 pb-7 sm:px-8 sm:pb-8 {{ isset($header) || $title ? '' : 'pt-6 sm:pt-8' }}">
                {{ $slot }}
            </div>
        </section>
    </div>
</div>
