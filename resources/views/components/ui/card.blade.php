@props(['glow' => false])

<div {{ $attributes->merge(['class' => 'animate-fade-in-up relative overflow-hidden rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.045] p-6 shadow-sm dark:shadow-2xl dark:shadow-black/20 backdrop-blur-xl dark:backdrop-blur-2xl sm:p-8']) }}>
    @if ($glow)
        <div class="pointer-events-none absolute -right-20 -top-32 h-72 w-72 rounded-full bg-indigo-400/10 dark:bg-indigo-500/20 blur-3xl"></div>
    @endif

    <div class="relative">
        {{ $slot }}
    </div>
</div>
