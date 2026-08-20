@props(['id', 'wireModel', 'placeholder' => '••••••••', 'autocomplete' => 'current-password', 'required' => true])

<div class="relative" x-data="{ show: false }">
    <input
        :type="show ? 'text' : 'password'"
        wire:model="{{ $wireModel }}"
        id="{{ $id }}"
        name="{{ $id }}"
        @if ($required) required @endif
        autocomplete="{{ $autocomplete }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full px-4 py-3 pr-11 rounded-xl bg-white dark:bg-slate-950/60 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm']) }}
    />
    <button
        type="button"
        @click="show = !show"
        tabindex="-1"
        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 dark:text-slate-500 transition-colors hover:text-slate-700 dark:hover:text-slate-300"
        :aria-label="show ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
    >
        <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        <svg x-show="show" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.774 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
        </svg>
    </button>
</div>
