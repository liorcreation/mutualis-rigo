<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="relative min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    <!-- Carte Glassmorphism centrée -->
    <div class="relative w-full max-w-md p-8 sm:p-10 rounded-3xl bg-slate-900/60 backdrop-blur-2xl border border-slate-800/80 shadow-2xl shadow-indigo-500/10">

        <!-- En-tête de la carte -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-emerald-400 p-0.5 shadow-lg shadow-indigo-500/30 mb-4">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">
                Mot de passe oublié
            </h2>
            <p class="mt-3 text-xs text-slate-400 leading-5">
                Indiquez votre adresse e-mail, nous vous enverrons un lien pour choisir un nouveau mot de passe.
            </p>
        </div>

        <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

        <form wire:submit="sendPasswordResetLink" class="space-y-6">
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Adresse e-mail
                </label>
                <input
                    wire:model="email"
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    placeholder="nom@exemple.com"
                    class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-600 active:scale-[0.99] transition-all shadow-lg shadow-indigo-500/25 flex items-center justify-center gap-2 cursor-pointer">
                <span>Envoyer le lien de réinitialisation</span>
            </button>
        </form>

    </div>

</div>
