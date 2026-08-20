<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
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
                Vérifiez votre e-mail
            </h2>
            <p class="mt-3 text-xs text-slate-400 leading-5">
                Merci de votre inscription ! Cliquez sur le lien que nous venons de vous envoyer par e-mail. Vous ne l'avez pas reçu ? Nous pouvons vous en renvoyer un.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-center text-xs font-semibold text-emerald-300">
                Un nouveau lien de vérification a été envoyé à l'adresse e-mail fournie lors de votre inscription.
            </div>
        @endif

        <div class="flex items-center justify-between gap-4">
            <button wire:click="logout" type="submit" class="text-xs font-medium text-slate-400 hover:text-white transition-colors cursor-pointer">
                Déconnexion
            </button>

            <button wire:click="sendVerification" type="button" class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all hover:from-indigo-500 hover:to-indigo-600 active:scale-[0.99] cursor-pointer">
                <span>Renvoyer l'e-mail</span>
            </button>
        </div>

    </div>

</div>
