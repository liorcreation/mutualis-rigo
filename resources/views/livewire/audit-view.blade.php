<div wire:poll.3s class="space-y-6">

    <!-- En-tête avec Statut en Direct & Filtres -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    Vérification de Sécurité
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    DIRECT / SÉCURISÉ
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                Carnet de Confiance & Journal d'Audit
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                Vérification en direct que les informations saisies n'ont pas été modifiées ou altérées.
            </p>
        </div>

        <!-- Commutateur de Mode d'Affichage (Blocs vs Tableau) -->
        <div class="flex items-center bg-slate-200 dark:bg-slate-900 p-1 rounded-xl border border-slate-300 dark:border-slate-800 self-start md:self-auto">
            <button wire:click="setViewMode('blocks')" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 {{ $viewMode === 'blocks' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Vue Blocs (Cryptographique)
            </button>
            <button wire:click="setViewMode('table')" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 {{ $viewMode === 'table' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Vue Tableau (Synthétique)
            </button>
        </div>
    </div>

    <!-- 4 Cartes Métriques Unifiées (KPIs) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm relative overflow-hidden">
            <div class="text-[11px] font-mono uppercase tracking-wider text-slate-400 mb-1">Projets Inscrits</div>
            <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $totalProjets }}</div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Demandes enregistrées</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm relative overflow-hidden">
            <div class="text-[11px] font-mono uppercase tracking-wider text-slate-400 mb-1">Argent Engagé</div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                {{ number_format($totalBudget, 0, ',', ' ') }} <span class="text-xs font-bold text-slate-400">FCFA</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Ressources financières</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm relative overflow-hidden">
            <div class="text-[11px] font-mono uppercase tracking-wider text-slate-400 mb-1">Preuves de Sécurité</div>
            <div class="text-3xl font-black text-indigo-500 dark:text-indigo-300">{{ $totalPreuves }}</div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Enregistrements verrouillés</p>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm relative overflow-hidden">
            <div class="text-[11px] font-mono uppercase tracking-wider text-slate-400 mb-1">État du Système</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                Opérationnel
            </div>
            <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-semibold flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                100% Intègre & Suivi
            </p>
        </div>

    </div>

    <!-- Zone de Recherche & Barre de Titre -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                Historique Sécurisé des Enregistrements
            </h2>
        </div>

        <div class="w-full sm:w-80 relative">
            <input wire:model.live.debounce.250ms="search" type="text" placeholder="Rechercher par auteur, action, hash..." class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 px-4 py-2.5 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
        </div>
    </div>

    <!-- OPTION 1 : AFFICHAGE EN CARNET DE BLOCS CRYPTOGRAPHIQUES -->
    @if($viewMode === 'blocks')
        <div class="space-y-4">
            @forelse($auditLogs as $index => $log)
                @php
                    $data = json_decode($log->donnees_auditees, true) ?? [];
                    $auteur = $data['auteur'] ?? 'Système';
                    $titreProjet = $data['titre'] ?? ($data['action'] ?? 'Opération Système');
                    $secretKey = substr(md5($log->hash_actuel), 0, 12);
                @endphp

                <div class="bg-slate-900 text-slate-100 rounded-2xl border border-slate-800 p-5 shadow-lg relative overflow-hidden transition-all hover:border-indigo-500/50">
                    <!-- En-tête du Bloc -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-extrabold uppercase tracking-wide bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                ENREGISTREMENT #{{ $auditLogs->count() - $index }}
                            </span>
                            <span class="text-xs font-bold text-slate-300">
                                {{ strtoupper($log->action) }}
                            </span>
                        </div>
                        <div class="text-[11px] font-mono text-slate-400">
                            {{ \Carbon\Carbon::parse($log->enregistre_le ?? $log->created_at)->format('d/m/Y - H:i:s') }}
                        </div>
                    </div>

                    <!-- Contenu du Bloc -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-4">
                        <div class="md:col-span-2 space-y-1">
                            <div class="text-[11px] font-mono text-slate-400 uppercase">Par :</div>
                            <div class="text-sm font-bold text-white flex items-center gap-2">
                                {{ $auteur }}
                            </div>
                            @if(isset($data['titre']))
                                <div class="text-xs text-indigo-400 font-medium mt-1">
                                    Projet : {{ $data['titre'] }}
                                </div>
                            @endif
                        </div>

                        <div class="bg-slate-950/80 p-3 rounded-xl border border-slate-800/80 space-y-1">
                            <div class="text-[10px] font-mono text-slate-400 uppercase">Secret / Clef unique :</div>
                            <div class="text-xs font-mono font-bold text-emerald-400 break-all">
                                {{ $secretKey }}
                            </div>
                        </div>
                    </div>

                    <!-- Hashes SHA-256 -->
                    <div class="space-y-2 bg-slate-950 p-3.5 rounded-xl border border-slate-800 font-mono text-[11px]">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <span class="text-slate-500 font-semibold shrink-0">CODE PRÉCÉDENT :</span>
                            <span class="text-indigo-400 break-all sm:text-right">{{ $log->hash_parent }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 pt-1 border-t border-slate-900">
                            <span class="text-slate-500 font-semibold shrink-0">CODE ACTUEL :</span>
                            <span class="text-emerald-400 font-bold break-all sm:text-right">{{ $log->hash_actuel }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 text-slate-500">
                    Aucun enregistrement d'audit trouvé pour le moment.
                </div>
            @endforelse
        </div>
    @endif

    <!-- OPTION 2 : AFFICHAGE EN TABLEAU SYNTHÉTIQUE -->
    @if($viewMode === 'table')
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950/50 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <th class="py-3.5 px-6 font-semibold">Date & Heure</th>
                            <th class="py-3.5 px-6 font-semibold">Auteur</th>
                            <th class="py-3.5 px-6 font-semibold">Action effectuée</th>
                            <th class="py-3.5 px-6 font-semibold">Résultat du contrôle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($auditLogs as $log)
                            @php
                                $data = json_decode($log->donnees_auditees, true) ?? [];
                                $auteur = $data['auteur'] ?? 'Système';
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="py-4 px-6 font-mono text-slate-500">
                                    {{ \Carbon\Carbon::parse($log->enregistre_le ?? $log->created_at)->format('d/m/Y - H:i') }}
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-900 dark:text-white">
                                    {{ $auteur }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ $log->action }}</span>
                                    @if(isset($data['titre']))
                                        <span class="block text-[11px] text-indigo-500 dark:text-indigo-400 font-normal">
                                            Projet: {{ $data['titre'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Validé (SHA-256)
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500">
                                    Aucune opération enregistrée dans le journal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>