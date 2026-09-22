<?php

declare(strict_types=1);

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $accountType = 'personne_physique';
    public string $nomEntreprise = '';
    public string $rneSiret = '';
    public string $secteurActivite = '';
    public string $representantLegal = '';
    public string $siteWeb = '';

    public function updatedAccountType(): void
    {
        $this->resetValidation();
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'accountType' => [Rule::in([
                UserRole::PERSONNE_PHYSIQUE->value,
                UserRole::PERSONNE_MORALE->value,
            ])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'nomEntreprise' => [Rule::requiredIf(fn (): bool => $this->accountType === UserRole::PERSONNE_MORALE->value), 'nullable', 'string', 'max:255'],
            'rneSiret' => ['nullable', 'string', 'max:80'],
            'secteurActivite' => ['nullable', 'string', 'max:150'],
            'representantLegal' => [Rule::requiredIf(fn (): bool => $this->accountType === UserRole::PERSONNE_MORALE->value), 'nullable', 'string', 'max:150'],
            'siteWeb' => ['nullable', 'url', 'max:255'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create([
            'name' => $validated['accountType'] === UserRole::PERSONNE_MORALE->value
                ? $validated['nomEntreprise']
                : $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['accountType'],
        ]);

        $user->profile()->create([
            'nom_entreprise' => $validated['nomEntreprise'] ?: null,
            'rne_siret' => $validated['rneSiret'] ?: null,
            'secteur_activite' => $validated['secteurActivite'] ?: null,
            'representant_legal' => $validated['representantLegal'] ?: null,
            'site_web' => $validated['siteWeb'] ?: null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('projects.index', absolute: false), navigate: true);
    }
}; ?>

<div class="relative min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    <!-- Carte Glassmorphism centrée -->
    <div class="relative w-full max-w-md p-8 sm:p-10 rounded-3xl bg-white/70 dark:bg-slate-900/60 backdrop-blur-2xl border border-white/40 dark:border-slate-800/80 shadow-2xl shadow-indigo-500/10">

        <!-- En-tête de la carte -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-emerald-400 p-0.5 shadow-lg shadow-indigo-500/30 mb-4">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Créer un compte
            </h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 font-mono">
                Rejoignez le registre de mutualisation
            </p>
        </div>

        <form wire:submit="register" class="space-y-6">

            <div>
                <label for="accountType" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Type de profil
                </label>
                <select wire:model.live="accountType" id="accountType" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-950/60 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm">
                    <option value="personne_physique">Personne physique</option>
                    <option value="personne_morale">Personne morale / entreprise</option>
                </select>
                <x-input-error :messages="$errors->get('accountType')" class="mt-2" />
            </div>

            @if ($accountType === 'personne_morale')
                <div class="rounded-2xl border border-amber-200 dark:border-amber-400/20 bg-amber-50/70 dark:bg-amber-400/[0.06] p-4">
                    <p class="text-xs font-bold text-amber-800 dark:text-amber-200">Compte entreprise</p>
                    <p class="mt-1 text-[11px] leading-5 text-amber-700/80 dark:text-amber-200/70">Ces informations seront vérifiées avant l’accès aux contributions sensibles.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="nomEntreprise" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Nom de l’entreprise</label>
                        <input wire:model.live="nomEntreprise" id="nomEntreprise" type="text" placeholder="Ex. RIGO Conseil SARL" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-950/60 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm" />
                        <x-input-error :messages="$errors->get('nomEntreprise')" class="mt-2" />
                    </div>
                    <div>
                        <label for="rneSiret" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">RNE / identifiant</label>
                        <input wire:model.live="rneSiret" id="rneSiret" type="text" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-950/60 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm" />
                    </div>
                    <div>
                        <label for="secteurActivite" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Secteur d’activité</label>
                        <input wire:model.live="secteurActivite" id="secteurActivite" type="text" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-950/60 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm" />
                    </div>
                    <div>
                        <label for="representantLegal" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Représentant légal</label>
                        <input wire:model.live="representantLegal" id="representantLegal" type="text" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-950/60 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm" />
                        <x-input-error :messages="$errors->get('representantLegal')" class="mt-2" />
                    </div>
                    <div>
                        <label for="siteWeb" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Site web</label>
                        <input wire:model.live="siteWeb" id="siteWeb" type="url" placeholder="https://" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-950/60 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm" />
                        <x-input-error :messages="$errors->get('siteWeb')" class="mt-2" />
                    </div>
                </div>
            @endif

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    {{ $accountType === 'personne_morale' ? 'Nom du contact' : 'Nom complet' }}
                </label>
                <input
                    wire:model="name"
                    id="name"
                    type="text"
                    name="name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Jeanne Dupont"
                    class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-950/60 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm"
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Adresse e-mail
                </label>
                <input
                    wire:model="email"
                    id="email"
                    type="email"
                    name="email"
                    required
                    autocomplete="username"
                    placeholder="nom@exemple.com"
                    class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-950/60 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all text-sm"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Mot de passe
                </label>
                <x-ui.password-input id="password" wireModel="password" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Confirmer le mot de passe
                </label>
                <x-ui.password-input id="password_confirmation" wireModel="password_confirmation" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('login') }}" wire:navigate class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline transition-all">
                    Déjà inscrit ?
                </a>

                <button type="submit" class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition-all hover:from-indigo-500 hover:to-indigo-600 active:scale-[0.99] cursor-pointer">
                    <span>Créer mon compte</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </button>
            </div>

        </form>

    </div>

</div>
