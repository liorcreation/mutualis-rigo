<div class="space-y-8 text-slate-100">

    {{-- En-tête Espace Audit --}}
    <div class="bg-white/[0.045] rounded-3xl p-6 sm:p-8 shadow-2xl shadow-black/20 border border-white/10 backdrop-blur-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/10 text-indigo-300 text-[10px] font-mono font-bold uppercase rounded-full border border-indigo-500/20 mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                VÉRIFICATION DE SÉCURITÉ
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Carnet de Confiance Sécurisé</h1>
            <p class="text-slate-400 text-xs sm:text-sm mt-1">
                Vérification en direct que les informations n'ont pas été modifiées.
            </p>
        </div>
        <div class="px-4 py-2.5 bg-slate-950/80 rounded-2xl border border-slate-800/80 text-right">
            <span class="text-[10px] text-slate-500 font-mono block uppercase tracking-wider">ÉTAT DE LA SÉCURITÉ</span>
            <span class="text-xs font-bold text-emerald-400 font-mono flex items-center gap-1.5 justify-end">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                TOUT EST SÉCURISÉ
            </span>
        </div>
    </div>

    {{-- Métriques du Système --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white/[0.045] p-6 rounded-3xl border border-white/10 backdrop-blur-2xl">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 font-mono">Projets Inscrits</span>
            <div class="text-3xl font-black text-white mt-2">{{ $totalProjets }}</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Demandes enregistrées</span>
        </div>

        <div class="bg-white/[0.045] p-6 rounded-3xl border border-white/10 backdrop-blur-2xl">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 font-mono">Argent Engagé</span>
            <div class="text-3xl font-black text-emerald-400 mt-2 font-mono">
                {{ number_format($totalBudget, 0, ',', ' ') }} <span class="text-sm font-normal text-slate-500">FCFA</span>
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">Ressources financières</span>
        </div>

        <div class="bg-white/[0.045] p-6 rounded-3xl border border-white/10 backdrop-blur-2xl">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 font-mono">Preuves de Sécurité</span>
            <div class="text-3xl font-black text-indigo-400 mt-2 font-mono">{{ $auditLogs->count() }}</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Enregistrements verrouillés</span>
        </div>
    </div>

    {{-- Journal de Hachage --}}
    <div class="bg-white/[0.045] rounded-3xl shadow-2xl shadow-black/20 border border-white/10 backdrop-blur-2xl p-6 sm:p-8 font-mono text-xs space-y-4">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <h3 class="font-black text-slate-200 text-sm tracking-wide">Historique Sécurisé</h3>
            <span class="bg-indigo-950/80 text-indigo-300 text-[10px] px-3 py-1 rounded-full border border-indigo-800/60 font-bold">
                SÉCURITÉ MAXIMALE
            </span>
        </div>

        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
            @forelse($auditLogs as $audit)
                @php
                    $donnees = is_array($audit->donnees_auditees)
                        ? $audit->donnees_auditees
                        : json_decode($audit->donnees_auditees ?? '{}', true);
                @endphp
                <div class="bg-slate-950/90 border border-slate-800/90 rounded-2xl p-4 text-[11px] space-y-2.5 transition-all hover:border-slate-700">
                    <div class="flex justify-between items-center text-indigo-400 font-bold border-b border-slate-800/60 pb-2 text-[10px]">
                        <span>ENREGISTREMENT #{{ $audit->id }} — {{ $audit->action ?? 'ACTION_REGISTRE' }}</span>
                        <span class="text-slate-500 font-normal">
                            {{ $audit->created_at?->format('d/m/Y H:i:s') ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-slate-300 pt-1">
                        <div>
                            <span class="text-slate-500 block text-[10px]">PAR :</span>
                            <span class="font-medium text-slate-200">{{ $donnees['auteur'] ?? 'Opérateur Système' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px]">SUJET :</span>
                            <span class="text-slate-300">{{ $donnees['titre'] ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-800/60 text-[10px]">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                            <span class="text-slate-500 w-28 shrink-0">CODE PRÉCÉDENT :</span>
                            <span x-data="{ copied: false }"
                                  @click="navigator.clipboard.writeText('{{ $audit->hash_parent ?? '0000000000000000000000000000000000000000000000000000000000000000' }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                  title="Cliquer pour copier"
                                  class="text-slate-400 bg-slate-900/90 px-2 py-1 rounded-lg truncate block w-full font-mono border border-slate-800/60 cursor-pointer hover:border-slate-700 transition-colors">
                                <span x-text="copied ? 'COPIÉ !' : '{{ $audit->hash_parent ?? '0000000000000000000000000000000000000000000000000000000000000000' }}'"></span>
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                            <span class="text-emerald-400 w-28 shrink-0 font-bold">CODE ACTUEL :</span>
                            <span x-data="{ copied: false }"
                                  @click="navigator.clipboard.writeText('{{ $audit->hash_actuel ?? 'N/A' }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                  title="Cliquer pour copier"
                                  class="text-emerald-300 bg-emerald-950/20 px-2 py-1 rounded-lg truncate block w-full font-mono border border-emerald-900/30 cursor-pointer hover:border-emerald-700/50 transition-colors">
                                <span x-text="copied ? 'COPIÉ !' : '{{ $audit->hash_actuel ?? 'N/A' }}'"></span>
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-slate-500 text-xs italic">
                    Aucun enregistrement pour le moment.
                </div>
            @endforelse
        </div>
    </div>

</div>
