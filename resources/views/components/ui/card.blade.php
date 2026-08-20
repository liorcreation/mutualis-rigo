@props(['glow' => false])

<div {{ $attributes->merge(['class' => 'animate-fade-in-up relative overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] p-6 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-8']) }}>
    @if ($glow)
        <div class="pointer-events-none absolute -right-20 -top-32 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>
    @endif

    <div class="relative">
        {{ $slot }}
    </div>
</div>
