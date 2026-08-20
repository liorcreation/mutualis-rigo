@props(['variant' => 'primary'])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-xl text-sm font-semibold transition-all disabled:cursor-wait disabled:opacity-60';

    $variants = [
        'primary' => 'px-6 py-3.5 text-white bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-600 shadow-lg shadow-indigo-500/25 active:scale-[0.99]',
        'secondary' => 'px-6 py-3.5 text-slate-300 bg-white/[0.04] border border-white/10 hover:bg-white/10 hover:text-white',
        'danger' => 'px-6 py-3.5 text-rose-300 bg-rose-500/15 hover:bg-rose-500 hover:text-white',
        'ghost' => 'px-4 py-2 text-slate-400 hover:text-white hover:bg-white/10',
    ];

    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
    {{ $slot }}
</button>
