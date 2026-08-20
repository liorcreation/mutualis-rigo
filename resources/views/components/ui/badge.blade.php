@props(['color' => 'slate', 'label' => null])

@php
    $classes = match ($color) {
        'indigo' => 'border-indigo-400/20 bg-indigo-400/10 text-indigo-300',
        'emerald' => 'border-emerald-400/20 bg-emerald-400/10 text-emerald-300',
        'amber' => 'border-amber-400/20 bg-amber-400/10 text-amber-300',
        'fuchsia' => 'border-fuchsia-400/20 bg-fuchsia-400/10 text-fuchsia-300',
        'rose' => 'border-rose-400/20 bg-rose-400/10 text-rose-300',
        default => 'border-slate-400/20 bg-slate-400/10 text-slate-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex w-fit items-center rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-wider {$classes}"]) }}>
    {{ $label ?? $slot }}
</span>
