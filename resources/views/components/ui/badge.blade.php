@props(['color' => 'slate', 'label' => null])

@php
    $classes = match ($color) {
        'indigo' => 'border-indigo-200 bg-indigo-50 text-indigo-600 dark:border-indigo-400/20 dark:bg-indigo-400/10 dark:text-indigo-300',
        'emerald' => 'border-emerald-200 bg-emerald-50 text-emerald-600 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-300',
        'amber' => 'border-amber-200 bg-amber-50 text-amber-600 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-300',
        'fuchsia' => 'border-fuchsia-200 bg-fuchsia-50 text-fuchsia-600 dark:border-fuchsia-400/20 dark:bg-fuchsia-400/10 dark:text-fuchsia-300',
        'rose' => 'border-rose-200 bg-rose-50 text-rose-600 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-300',
        default => 'border-slate-200 bg-slate-100 text-slate-600 dark:border-slate-400/20 dark:bg-slate-400/10 dark:text-slate-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex w-fit items-center rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-wider {$classes}"]) }}>
    {{ $label ?? $slot }}
</span>
