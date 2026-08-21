<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-black text-slate-900 dark:text-white">
            {{ __('Informations du profil') }}
        </h2>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            {{ __("Mettez à jour le nom et l'adresse e-mail de votre compte.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-5">
        <label class="block">
            <span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('Nom') }}</span>
            <input wire:model="name" id="name" name="name" type="text" required autofocus autocomplete="name"
                class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950/70 px-4 py-3.5 text-sm text-slate-900 dark:text-white outline-none transition placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('name') border-rose-400/70 @enderror">
            @error('name')<span class="mt-2 block text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</span>@enderror
        </label>

        <label class="block">
            <span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('Adresse e-mail') }}</span>
            <input wire:model="email" id="email" name="email" type="email" required autocomplete="username"
                class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950/70 px-4 py-3.5 text-sm text-slate-900 dark:text-white outline-none transition placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('email') border-rose-400/70 @enderror">
            @error('email')<span class="mt-2 block text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</span>@enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-3 rounded-2xl border border-amber-200 dark:border-amber-400/20 bg-amber-50 dark:bg-amber-400/10 px-4 py-3">
                    <p class="text-xs text-amber-700 dark:text-amber-200">
                        {{ __('Votre adresse e-mail n’est pas vérifiée.') }}

                        <button wire:click.prevent="sendVerification" class="font-bold underline decoration-dotted underline-offset-2 hover:text-amber-900 dark:hover:text-white">
                            {{ __('Cliquez ici pour renvoyer l’e-mail de vérification.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-300">
                            {{ __('Un nouveau lien de vérification a été envoyé à votre adresse e-mail.') }}
                        </p>
                    @endif
                </div>
            @endif
        </label>

        <div class="flex items-center gap-4">
            <button type="submit" class="rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-6 py-3 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-950/40 transition hover:from-indigo-400 hover:to-fuchsia-400">
                {{ __('Enregistrer') }}
            </button>

            <div x-data="{ shown: false, timeout: null }"
                 x-init="@this.on('profile-updated', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
                 x-show.transition.out.opacity.duration.1500ms="shown"
                 x-transition:leave.opacity.duration.1500ms
                 style="display: none;"
                 class="text-xs font-bold text-emerald-600 dark:text-emerald-300">
                {{ __('Enregistré.') }}
            </div>
        </div>
    </form>
</section>
