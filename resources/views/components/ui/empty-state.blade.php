@props(['heading', 'text' => null, 'ctaLabel' => null, 'ctaHref' => null, 'bordered' => true])

<div {{ $attributes->merge(['class' => ($bordered ? 'rounded-3xl border border-dashed border-slate-300 dark:border-white/15 ' : '').'px-6 py-16 text-center']) }}>
    @isset($icon)
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-white/[0.04] text-slate-500 dark:text-slate-400">
            {{ $icon }}
        </div>
    @endisset

    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $heading }}</p>

    @if ($text)
        <p class="mt-2 text-xs text-slate-500">{{ $text }}</p>
    @endif

    @if ($ctaLabel && $ctaHref)
        <a href="{{ $ctaHref }}" wire:navigate class="mt-5 inline-flex rounded-xl bg-indigo-500 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-400">
            {{ $ctaLabel }}
        </a>
    @endif

    {{ $slot }}
</div>
