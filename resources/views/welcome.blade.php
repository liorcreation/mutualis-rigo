<x-app-layout>
    <div class="relative overflow-hidden pt-12 pb-20 bg-slate-950 text-white min-h-screen">

        <!-- Effet de fond lumineux -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-tr from-indigo-600/20 to-emerald-500/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <!-- Hero Section -->
            <div class="text-center max-w-3xl mx-auto space-y-6">

                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-mono font-semibold uppercase tracking-wider">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Plateforme de Partage et de Suivi
                </div>

                <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-tight text-white">
                    La transparence et le suivi clair de vos <span class="bg-gradient-to-r from-indigo-400 to-emerald-400 bg-clip-text text-transparent">Projets Partagés</span>.
                </h1>

                <p class="text-slate-400 text-sm sm:text-base leading-relaxed font-normal max-w-2xl mx-auto">
                    Mutualis protège chaque demande et chaque aide pour garantir qu'aucun changement n'est fait en cachette. C'est simple, fiable et sécurisé pour tout le monde.
                </p>

                <!-- Boutons d'Action -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    <a href="{{ route('projects.index') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold text-sm shadow-lg shadow-indigo-500/25 hover:scale-[1.02] transition-all">
                        Voir toutes les demandes →
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 font-semibold text-sm hover:bg-slate-800 hover:text-white shadow-sm transition-all">
                        Mon Espace
                    </a>
                </div>

            </div>

            <!-- Grille de Statistiques / Garanties -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16">
                <div class="p-6 rounded-3xl bg-white/[0.045] border border-white/10 backdrop-blur-2xl">
                    <div class="text-xs font-mono uppercase tracking-wider text-slate-500 mb-2">Suivi</div>
                    <div class="text-2xl font-black text-white">100% Suivis</div>
                    <p class="text-xs text-slate-400 mt-2">Chaque projet possède son propre espace de suivi unique.</p>
                </div>

                <div class="p-6 rounded-3xl bg-white/[0.045] border border-white/10 backdrop-blur-2xl">
                    <div class="text-xs font-mono uppercase tracking-wider text-slate-500 mb-2">Sécurité</div>
                    <div class="text-2xl font-black text-emerald-400 font-mono">Fiable</div>
                    <p class="text-xs text-slate-400 mt-2">Protection renforcée contre les modifications non autorisées.</p>
                </div>

                <div class="p-6 rounded-3xl bg-white/[0.045] border border-white/10 backdrop-blur-2xl">
                    <div class="text-xs font-mono uppercase tracking-wider text-slate-500 mb-2">Collaboration</div>
                    <div class="text-2xl font-black text-indigo-400">Entre Acteurs</div>
                    <p class="text-xs text-slate-400 mt-2">Validation et participation simple entre les membres.</p>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-20">
                <div class="text-center mb-10">
                    <h2 class="text-xl sm:text-2xl font-black text-white">Conçu pour être simple et efficace</h2>
                    <p class="text-slate-400 text-xs mt-1">Les points forts de la plateforme.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 rounded-3xl bg-white/[0.03] border border-white/10 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-white">Registre Centralisé</h3>
                        <p class="text-slate-400 text-xs leading-relaxed">Regroupement clair de tous les besoins et aides financières en un seul endroit.</p>
                    </div>

                    <div class="p-6 rounded-3xl bg-white/[0.03] border border-white/10 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-white">Suivi en Direct</h3>
                        <p class="text-slate-400 text-xs leading-relaxed">Chaque action est enregistrée instantanément pour que tout soit parfaitement à jour.</p>
                    </div>

                    <div class="p-6 rounded-3xl bg-white/[0.03] border border-white/10 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-fuchsia-500/10 border border-fuchsia-500/20 flex items-center justify-center text-fuchsia-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-white">Historique Sécurisé</h3>
                        <p class="text-slate-400 text-xs leading-relaxed">Un journal d'activités clair pour vérifier facilement toutes les opérations réalisées.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
