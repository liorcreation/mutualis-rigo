<div class="space-y-8 font-sans antialiased text-slate-800 dark:text-slate-100 animate-fade-in-up">

    {{-- Bannière d'en-tête du registre --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-800 animate-gradient">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-indigo-500/20 via-purple-500/10 to-transparent pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 text-[10px] font-mono font-bold uppercase tracking-widest rounded-full border border-indigo-500/30 backdrop-blur-md shadow-inner">
                        ESPACE DE PARTAGE
                    </span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-indigo-200">
                    Liste des Demandes et Sécurité
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                    Partage d'aide (personnel, argent, matériel) en toute sécurité cryptographique.
                </p>
            </div>
            <div class="flex items-center gap-2.5 px-4 py-2 bg-slate-950/80 rounded-full border border-emerald-500/30 self-start sm:self-auto shadow-lg shadow-emerald-950/50 animate-glow">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-mono font-bold tracking-wider text-emerald-400">SYSTÈME ACTIF</span>
            </div>
        </div>
    </div>

    {{-- Console de Simulation d'Acteur --}}
    <div class="bg-amber-500/10 border border-amber-500/20 dark:bg-amber-950/20 dark:border-amber-900/40 rounded-2xl p-5 shadow-sm backdrop-blur-md hover:border-amber-500/40 transition-all duration-300">
        <div class="flex items-start gap-3.5">
            <div class="p-2.5 bg-amber-500/20 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 rounded-xl shrink-0 mt-0.5 border border-amber-500/30 dark:border-amber-800/40 shadow-sm">
                <svg class="w-5 h-5 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div class="space-y-2 flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 dark:text-amber-400">Changer de Profil (Test)</h3>
                    <span class="text-[10px] font-mono font-semibold bg-amber-500/20 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 px-2.5 py-0.5 rounded-full border border-amber-500/30 dark:border-amber-800/60 shadow-sm">Mode Démo</span>
                </div>
                <p class="text-xs text-amber-900/80 dark:text-amber-200/70 leading-relaxed">
                    Choisissez un profil pour tester l'envoi d'une demande et voir la sécurité s'activer toute seule.
                </p>
                <div class="pt-1 max-w-md">
                    <select wire:model.live="selectedActorId" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-800 py-2.5 px-3 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 font-medium cursor-pointer transition-all duration-200 outline-none hover:border-amber-400">
                        @foreach($actors as $actor)
                            <option value="{{ $actor['id'] }}">
                                {{ $actor['nom'] }} — [{{ $actor['role'] }}]
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Notification Flash --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 p-4 border border-emerald-200 dark:border-emerald-800/80 text-xs font-semibold text-emerald-800 dark:text-emerald-300 shadow-md flex items-center justify-between animate-glow">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-600 dark:text-emerald-500 hover:text-emerald-800 dark:hover:text-emerald-300 font-bold text-sm leading-none transition-colors hover:scale-125 duration-150">&times;</button>
        </div>
    @endif

    {{-- Grille principale avec alignement en haut --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Formulaire de publication --}}
        <div class="lg:col-span-5 h-fit self-start sticky top-24 bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500/30 transition-all duration-300">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-5 border-b border-slate-200 dark:border-slate-800 pb-3.5 flex items-center gap-2.5">
                <span class="p-2 bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-200 dark:border-indigo-900/50 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </span>
                Demander de l'aide
            </h2>
            
            <form wire:submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Titre du Projet</label>
                    <input type="text" wire:model="titre" placeholder="ex: Plateforme IoT Intelligente" class="w-full rounded-xl border border-slate-300 dark:border-slate-800 shadow-sm text-xs p-3 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 focus:scale-[1.01] outline-none transition-all duration-200 placeholder:text-slate-400 dark:placeholder:text-slate-600">
                    @error('titre') <span class="text-rose-500 dark:text-rose-400 text-[11px] mt-1 block font-medium animate-pulse">{{ $message }}</span> @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">1. Personnes (RH)</label>
                        <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950/80 text-blue-600 dark:text-blue-400 text-[9px] font-bold uppercase rounded border border-blue-200 dark:border-blue-900/50">Humain</span>
                    </div>
                    <textarea wire:model="rh" rows="2" placeholder="ex: Développeur, Designer" class="w-full rounded-xl border border-slate-300 dark:border-slate-800 shadow-sm text-xs p-3 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 focus:scale-[1.01] outline-none transition-all duration-200 placeholder:text-slate-400 dark:placeholder:text-slate-600"></textarea>
                    @error('rh') <span class="text-rose-500 dark:text-rose-400 text-[11px] mt-1 block font-medium animate-pulse">{{ $message }}</span> @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">2. Argent (Finances)</label>
                        <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 text-[9px] font-bold uppercase rounded border border-emerald-200 dark:border-emerald-900/50">Finance</span>
                    </div>
                    <div class="relative">
                        <input type="number" wire:model="finance" placeholder="ex: 150000" class="w-full rounded-xl border border-slate-300 dark:border-slate-800 shadow-sm text-xs p-3 pr-16 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 focus:scale-[1.01] outline-none transition-all duration-200 placeholder:text-slate-400 dark:placeholder:text-slate-600">
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">FCFA</span>
                        </div>
                    </div>
                    @error('finance') <span class="text-rose-500 dark:text-rose-400 text-[11px] mt-1 block font-medium animate-pulse">{{ $message }}</span> @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">3. Matériel</label>
                        <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/80 text-purple-600 dark:text-purple-400 text-[9px] font-bold uppercase rounded border border-purple-200 dark:border-purple-900/50">Matériel</span>
                    </div>
                    <textarea wire:model="materiel" rows="2" placeholder="ex: Ordinateurs, Serveurs" class="w-full rounded-xl border border-slate-300 dark:border-slate-800 shadow-sm text-xs p-3 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 focus:scale-[1.01] outline-none transition-all duration-200 placeholder:text-slate-400 dark:placeholder:text-slate-600"></textarea>
                    @error('materiel') <span class="text-rose-500 dark:text-rose-400 text-[11px] mt-1 block font-medium animate-pulse">{{ $message }}</span> @enderror
                </div>

                {{-- Bouton d'Enregistrement Magnétique & Animé --}}
                <button type="submit" wire:loading.attr="disabled" wire:target="submit" class="w-full py-3.5 px-5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center gap-2.5 mt-2 cursor-pointer group">
                    <svg wire:loading.remove wire:target="submit" class="w-4 h-4 transition-transform group-hover:rotate-12 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <svg wire:loading wire:target="submit" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="submit">Enregistrer et Sécuriser</span>
                    <span wire:loading wire:target="submit">Sécurisation SHA-256...</span>
                </button>
            </form>
        </div>

        {{-- Tableau du Registre --}}
        <div class="lg:col-span-7">
            <div class="bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 flex flex-col justify-between hover:border-indigo-500/30 transition-all duration-300">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white mb-5 border-b border-slate-200 dark:border-slate-800 pb-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-200 dark:border-indigo-900/50 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </span>
                            <span>Liste des Demandes</span>
                        </div>
                        <span class="text-[10px] font-mono px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-full font-bold">
                            {{ count($projets) }} Enregistrement(s)
                        </span>
                    </h2>
                    
                    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800/80 bg-slate-50 dark:bg-slate-950 shadow-inner">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100/80 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    <th class="py-3 px-3.5">Par</th>
                                    <th class="py-3 px-3.5">Détails</th>
                                    <th class="py-3 px-3.5">Montant</th>
                                    <th class="py-3 px-3.5 text-right">État</th>
                                </tr>
                            </thead>
                            <!-- x-init="autoAnimate($el)" pour l'animation automatique des nouvelles lignes -->
                            <tbody x-init="autoAnimate($el)" class="divide-y divide-slate-200 dark:divide-slate-800/60 text-xs">
                                @forelse($projets as $p)
                                    <tr class="hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 hover:-translate-y-0.5 transition-all duration-200 group cursor-pointer">
                                        <td class="py-3.5 px-3.5 font-medium text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                            <div class="font-semibold text-xs text-slate-900 dark:text-white group-hover:text-indigo-500 transition-colors">{{ $p->user?->name ?? $p->auteur ?? 'Utilisateur' }}</div>
                                            <div class="text-[10px] text-slate-500 font-mono">{{ $p->user?->email ?? 'N/A' }}</div>
                                        </td>
                                        
                                        <td class="py-3.5 px-3.5 space-y-1">
                                            <div class="font-bold text-slate-800 dark:text-slate-200 text-xs group-hover:text-indigo-400 transition-colors">{{ $p->titre }}</div>
                                            @if(!empty($p->description))
                                                <p class="text-slate-500 dark:text-slate-400 text-[11px] line-clamp-2">{{ $p->description }}</p>
                                            @endif
                                            
                                            <div class="flex flex-wrap gap-1 pt-1">
                                                @if(!empty($p->rh))
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50 shadow-sm" title="{{ $p->rh }}">
                                                        Humain
                                                    </span>
                                                @endif
                                                @if(($p->budget ?? $p->finance ?? 0) > 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50 shadow-sm">
                                                        Financier
                                                    </span>
                                                @endif
                                                @if(!empty($p->materiel_requis ?? $p->materiel))
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/50 shadow-sm" title="{{ $p->materiel_requis ?? $p->materiel }}">
                                                        Matériel
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td class="py-3.5 px-3.5 font-mono font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                            {{ number_format($p->budget ?? $p->finance ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        
                                        <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                            @php
                                                $statut = $p->statut?->value ?? 'brouillon';
                                                $statutClasses = match($statut) {
                                                    'valide', 'approuve' => 'bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/80',
                                                    'refuse', 'annule' => 'bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800/80',
                                                    default => 'bg-amber-50 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/80',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full border shadow-sm {{ $statutClasses }}">
                                                {{ ucfirst(str_replace('_', ' ', $statut)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-12 text-center text-slate-500 italic text-xs">
                                            Aucune demande enregistrée.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panneau d'Audit Cryptographique (Journal de Hashs) --}}
    <div class="bg-slate-900 text-slate-100 p-6 sm:p-7 rounded-3xl shadow-2xl border border-slate-800 font-mono text-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3.5 gap-3">
            <div class="flex items-center gap-2.5">
                <span class="p-2 bg-slate-950 text-indigo-400 rounded-xl border border-slate-800 shadow-sm">
                    <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
                <h3 class="font-bold text-slate-200 text-sm tracking-wide">Historique de Sécurité (Registre Immédiat)</h3>
            </div>
            <span class="bg-emerald-500/10 text-emerald-400 text-[10px] px-3 py-1 rounded-full border border-emerald-500/30 font-bold self-start sm:self-auto animate-glow">
                100% SÉCURISÉ SHA-256
            </span>
        </div>

        <p class="text-[11px] text-slate-400">
            Chaque action est enregistrée de manière à ce qu'on ne puisse pas la modifier en cachette.
        </p>

        <!-- x-init="autoAnimate($el)" pour l'apparition dynamique des blocs d'audit -->
        <div x-init="autoAnimate($el)" class="space-y-3 max-h-96 overflow-y-auto pr-1 custom-scrollbar">
            @forelse($audits as $audit)
                @php
                    $donnees = is_array($audit->donnees_auditees) 
                        ? $audit->donnees_auditees 
                        : json_decode($audit->donnees_auditees ?? '{}', true);
                @endphp
                <div class="bg-slate-950/80 border border-slate-800 hover:border-indigo-500/40 rounded-2xl p-4 text-[11px] space-y-2.5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="flex justify-between items-center text-indigo-400 font-bold border-b border-slate-800/80 pb-2 text-[10px]">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span>
                            ENREGISTREMENT #{{ $audit->id }} — {{ $audit->action }}
                        </span>
                        <span class="text-slate-500 font-normal">
                            {{ $audit->enregistre_le ?? ($audit->created_at ? $audit->created_at->format('d/m/Y H:i:s') : 'Récemment') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-slate-300 pt-1">
                        <div>
                            <span class="text-slate-500 block text-[10px]">QUI :</span>
                            <span class="font-medium text-slate-200">{{ $donnees['auteur'] ?? 'Système' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px]">PROJET :</span>
                            <span class="text-slate-300">{{ $donnees['titre'] ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-800/60 text-[10px]">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                            <span class="text-slate-500 w-28 shrink-0">CODE PRÉCÉDENT :</span>
                            <span x-data="{ copied: false }" 
                                  @click="navigator.clipboard.writeText('{{ $audit->hash_parent ?? '0000000000000000000000000000000000000000000000000000000000000000' }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                  title="Cliquer pour copier" 
                                  class="text-slate-400 bg-slate-900 px-2.5 py-1 rounded truncate block w-full font-mono border border-slate-800 select-all cursor-pointer hover:border-slate-600 transition-colors">
                                <span x-text="copied ? 'COPIÉ DANS LE PRESSE-PAPIERS !' : '{{ $audit->hash_parent ?? '0000000000000000000000000000000000000000000000000000000000000000' }}'" :class="copied ? 'text-emerald-400 font-bold' : ''"></span>
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                            <span class="text-amber-400 w-28 shrink-0 font-bold">CODE ACTUEL :</span>
                            <span x-data="{ copied: false }" 
                                  @click="navigator.clipboard.writeText('{{ $audit->hash_actuel }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                  title="Cliquer pour copier" 
                                  class="text-amber-300 bg-amber-950/40 px-2.5 py-1 rounded truncate block w-full font-mono border border-amber-900/50 select-all cursor-pointer hover:border-amber-600/60 transition-colors">
                                <span x-text="copied ? 'COPIÉ DANS LE PRESSE-PAPIERS !' : '{{ $audit->hash_actuel }}'" :class="copied ? 'text-emerald-400 font-bold' : ''"></span>
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-slate-500 text-xs italic">
                    Aucun enregistrement pour le moment.
                </div>
            @endforelse
        </div>
    </div>

</div>
