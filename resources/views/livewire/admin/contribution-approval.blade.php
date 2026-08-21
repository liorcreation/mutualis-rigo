<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        @if(session('approval-message'))
            <div class="mb-6 rounded-2xl border border-emerald-200 dark:border-emerald-400/20 bg-emerald-50 dark:bg-emerald-400/10 px-4 py-3 text-sm font-semibold text-emerald-700 dark:text-emerald-200">{{ session('approval-message') }}</div>
        @endif

        <x-ui.card glow>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-300">Centre de validation</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">Apports en attente</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">Contrôlez les propositions reçues et maintenez les indicateurs de mutualisation à jour.</p>
                </div>
                <div class="rounded-2xl border border-amber-200 dark:border-amber-400/20 bg-amber-50 dark:bg-amber-400/10 px-4 py-3 text-right">
                    <p class="text-2xl font-black text-amber-600 dark:text-amber-200">{{ $contributions->total() }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-amber-600/70 dark:text-amber-200/60">À traiter</p>
                </div>
            </div>
        </x-ui.card>

        <div class="mt-8 flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.04] p-2">
            <span class="px-3 text-[10px] font-black uppercase tracking-widest text-slate-500">Périmètre :</span>
            <button wire:click="$set('typeFilter', 'all')" type="button" class="rounded-xl px-3 py-2 text-xs font-bold transition {{ $typeFilter === 'all' ? 'bg-indigo-500 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white' }}">Tous</button>
            @foreach($availableTypes as $type)
                <button wire:click="$set('typeFilter', '{{ $type }}')" type="button" class="rounded-xl px-3 py-2 text-xs font-bold transition {{ $typeFilter === $type ? 'bg-indigo-500 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white' }}">{{ \App\Enums\ContributionType::from($type)->label() }}</button>
            @endforeach
        </div>

        <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.045] shadow-sm dark:shadow-xl">
            <div class="hidden grid-cols-[1.3fr_1fr_0.8fr_0.8fr_auto] gap-4 border-b border-slate-200 dark:border-white/10 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 md:grid">
                <span>Apporteur</span>
                <span>Projet</span>
                <span>Type</span>
                <span>Valeur</span>
                <span>Action</span>
            </div>
            <div class="divide-y divide-slate-200 dark:divide-white/10">
                @forelse($contributions as $contribution)
                    <div wire:key="approval-{{ $contribution->id }}" class="grid gap-4 px-5 py-5 md:grid-cols-[1.3fr_1fr_0.8fr_0.8fr_auto] md:items-center md:px-6">
                        <div>
                            <p class="text-sm font-black text-slate-900 dark:text-white">{{ $contribution->user?->name ?? 'Utilisateur' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $contribution->user?->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $contribution->project?->titre ?? 'Projet supprimé' }}</p>
                            <p class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $contribution->description_apport ?: 'Aucun détail fourni' }}</p>
                        </div>
                        <x-ui.badge :color="$contribution->type_apport->color()" :label="$contribution->type_apport->label()" />
                        <span class="text-sm font-black text-slate-900 dark:text-white">{{ $contribution->montant ? number_format((float) $contribution->montant, 0, ',', ' ').' FCFA' : '—' }}</span>
                        <div class="flex gap-2">
                            <button wire:click="review({{ $contribution->id }}, 'valide')" type="button" class="rounded-xl bg-emerald-50 dark:bg-emerald-500/15 px-3 py-2 text-[10px] font-black text-emerald-600 dark:text-emerald-300 transition hover:bg-emerald-500 hover:text-white">Valider</button>
                            <button wire:click="review({{ $contribution->id }}, 'refuse')" type="button" class="rounded-xl bg-rose-50 dark:bg-rose-500/15 px-3 py-2 text-[10px] font-black text-rose-600 dark:text-rose-300 transition hover:bg-rose-500 hover:text-white">Refuser</button>
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state :bordered="false" heading="Aucune contribution en attente" text="Toutes les propositions de votre périmètre ont été traitées." />
                @endforelse
            </div>
        </div>

        @if($contributions->hasPages())
            <div class="mt-6">{{ $contributions->links() }}</div>
        @endif
    </div>

    <x-ui.modal wireProperty="showReview" onClose="closeReview" title="Confirmer votre décision" :accent="$decision === 'valide' ? 'bg-emerald-400' : 'bg-rose-400'">
        <x-slot:header>
            <p class="text-[10px] font-black uppercase tracking-widest {{ $decision === 'valide' ? 'text-emerald-600 dark:text-emerald-300' : 'text-rose-600 dark:text-rose-300' }}">{{ $decision === 'valide' ? 'Validation' : 'Refus' }}</p>
            <h2 class="mt-2 text-xl font-black text-slate-900 dark:text-white">Confirmer votre décision</h2>
        </x-slot:header>

        <form wire:submit="saveDecision" class="space-y-5">
            <label class="block">
                <span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">Commentaire / motif @if($decision === 'refuse')<span class="text-rose-500 dark:text-rose-400">*</span>@else<span class="font-normal text-slate-400 dark:text-slate-600">(optionnel)</span>@endif</span>
                <textarea wire:model.live.debounce.250ms="commentaire" rows="4" placeholder="Expliquez votre décision à l'apporteur..." class="w-full resize-none rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950/70 px-4 py-3.5 text-sm text-slate-900 dark:text-white outline-none focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('commentaire') border-rose-400/70 @enderror"></textarea>
                @error('commentaire')
                    <span class="mt-2 block text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</span>
                @enderror
            </label>
            <button wire:loading.attr="disabled" type="submit" class="w-full rounded-2xl {{ $decision === 'valide' ? 'bg-emerald-500 hover:bg-emerald-400' : 'bg-rose-500 hover:bg-rose-400' }} px-5 py-3.5 text-sm font-black text-white transition disabled:opacity-50">
                <span wire:loading.remove>Confirmer</span>
                <span wire:loading>Traitement...</span>
            </button>
        </form>
    </x-ui.modal>
</div>
