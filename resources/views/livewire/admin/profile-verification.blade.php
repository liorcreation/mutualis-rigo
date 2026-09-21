<div class="min-h-screen bg-slate-50 px-4 py-8 dark:bg-slate-950 sm:px-6 lg:px-10">
    <div class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.28em] text-indigo-500">Confiance & conformité</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Vérification des profils</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">Contrôlez les profils avant de leur ouvrir l'accès aux apports financiers et matériels sensibles.</p>
            </div>
            <select wire:model.live="filter" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-900 dark:text-slate-200">
                <option value="pending">À vérifier</option>
                <option value="verified">Vérifiés</option>
                <option value="all">Tous les profils</option>
            </select>
        </div>

        @if (session('verification-message'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-300/20 dark:bg-emerald-400/10 dark:text-emerald-200">{{ session('verification-message') }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($profiles as $profile)
                <article class="rounded-[2rem] border border-slate-200/80 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-xl dark:border-white/10 dark:bg-slate-900/80">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="truncate text-base font-black text-slate-900 dark:text-white">{{ $profile->user?->name ?: ($profile->prenom.' '.$profile->nom) }}</p>
                            <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $profile->user?->email }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-wider {{ $profile->is_verified ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-200' : 'bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-200' }}">{{ $profile->is_verified ? 'Vérifié' : 'À vérifier' }}</span>
                    </div>
                    <div class="mt-5 space-y-2 text-xs text-slate-600 dark:text-slate-300">
                        <p><span class="font-bold text-slate-400">Type :</span> {{ $profile->nom_entreprise ? 'Personne morale' : 'Personne physique' }}</p>
                        <p><span class="font-bold text-slate-400">Téléphone :</span> {{ $profile->user?->telephone ?: 'Non renseigné' }}</p>
                        <p><span class="font-bold text-slate-400">Inscription :</span> {{ $profile->created_at?->format('d/m/Y') }}</p>
                    </div>
                    <div class="mt-6 flex gap-2">
                        @if (! $profile->is_verified)
                            <button wire:click="setVerification({{ $profile->id }}, true)" wire:loading.attr="disabled" class="flex-1 rounded-xl bg-indigo-600 px-3 py-2.5 text-xs font-black text-white transition hover:bg-indigo-500 disabled:opacity-50">Vérifier le profil</button>
                        @else
                            <button wire:click="setVerification({{ $profile->id }}, false)" wire:loading.attr="disabled" class="flex-1 rounded-xl border border-rose-200 px-3 py-2.5 text-xs font-black text-rose-600 transition hover:bg-rose-50 dark:border-rose-300/20 dark:text-rose-300 dark:hover:bg-rose-400/10">Retirer la vérification</button>
                        @endif
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 xl:col-span-3 rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-sm text-slate-500 dark:border-white/10 dark:bg-slate-900/70 dark:text-slate-400">Aucun profil ne correspond à ce filtre.</div>
            @endforelse
        </div>

        <div>{{ $profiles->links() }}</div>
    </div>
</div>
