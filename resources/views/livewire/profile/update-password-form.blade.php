<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-black text-white">
            {{ __('Mot de passe') }}
        </h2>

        <p class="mt-1 text-sm text-slate-400">
            {{ __('Utilisez un mot de passe long et aléatoire pour protéger votre compte.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-5">
        <label class="block">
            <span class="mb-2 block text-xs font-bold text-slate-300">{{ __('Mot de passe actuel') }}</span>
            <input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none transition focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('current_password') border-rose-400/70 @enderror">
            @error('current_password')<span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span>@enderror
        </label>

        <label class="block">
            <span class="mb-2 block text-xs font-bold text-slate-300">{{ __('Nouveau mot de passe') }}</span>
            <input wire:model="password" id="update_password_password" name="password" type="password" autocomplete="new-password"
                class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none transition focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('password') border-rose-400/70 @enderror">
            @error('password')<span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span>@enderror
        </label>

        <label class="block">
            <span class="mb-2 block text-xs font-bold text-slate-300">{{ __('Confirmer le mot de passe') }}</span>
            <input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none transition focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('password_confirmation') border-rose-400/70 @enderror">
            @error('password_confirmation')<span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span>@enderror
        </label>

        <div class="flex items-center gap-4">
            <button type="submit" class="rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-6 py-3 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-950/40 transition hover:from-indigo-400 hover:to-fuchsia-400">
                {{ __('Enregistrer') }}
            </button>

            <div x-data="{ shown: false, timeout: null }"
                 x-init="@this.on('password-updated', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
                 x-show.transition.out.opacity.duration.1500ms="shown"
                 x-transition:leave.opacity.duration.1500ms
                 style="display: none;"
                 class="text-xs font-bold text-emerald-300">
                {{ __('Enregistré.') }}
            </div>
        </div>
    </form>
</section>
