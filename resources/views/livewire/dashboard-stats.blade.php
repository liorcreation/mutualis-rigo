<div wire:poll.3s class="space-y-6">

    <!-- 1. CHIFFRES IMPORTANTS (EN TEMPS RÉEL) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Demandes en attente -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between transition-colors">
            <div>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Demandes en attente</p>
                <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $demandesEnAttente }}</p>
            </div>
            <div class="p-3 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-xl border border-transparent dark:border-indigo-800/40">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>

        <!-- Choses partagées -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between transition-colors">
            <div>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Choses partagées</p>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $chosesPartagees }}</p>
            </div>
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-xl border border-transparent dark:border-emerald-800/40">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
        </div>

        <!-- Aide apportée -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between transition-colors">
            <div>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Aide apportée</p>
                <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $totalAide }}</p>
            </div>
            <div class="p-3 bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-xl border border-transparent dark:border-amber-800/40">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>

    </div>

    <!-- 2. SECTION ACTIVITÉS ET RACCOURCIS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Dernières activités -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 transition-colors">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Dernières activités</h3>
                @if (Route::has('registry'))
                    <a href="{{ route('registry') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">Voir tout →</a>
                @endif
            </div>
            
            <div class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs">
                @forelse($dernieresActivites as $projet)
                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-slate-800 dark:text-slate-200">{{ $projet->titre }}</p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ $projet->description }} • {{ $projet->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2.5 py-1 text-[10px] font-bold {{ $projet->statut->value === 'en_cours_de_mutualisation' ? 'bg-emerald-50 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border-emerald-200' : 'bg-amber-50 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border-amber-200' }} rounded-full border">
                            {{ ucfirst(str_replace('_', ' ', $projet->statut->value)) }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 text-center py-4">Aucune activité récente.</p>
                @endforelse
            </div>
        </div>

        <!-- Raccourcis -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 space-y-3 transition-colors">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Raccourcis</h3>
            
            @if (Route::has('registry'))
                <a href="{{ route('registry') }}" class="w-full flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-indigo-50/50 dark:hover:bg-slate-800/60 hover:border-indigo-300 dark:hover:border-slate-700 transition-all group">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">Demander de l'aide</span>
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </a>
            @endif

            @if(Route::has('admin.audit'))
                <a href="{{ route('admin.audit') }}" class="w-full flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-all group">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white">Vérification de sécurité</span>
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </a>
            @endif
        </div>

    </div>

</div>
