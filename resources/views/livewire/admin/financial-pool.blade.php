<div class="min-h-screen bg-slate-50 px-4 py-8 dark:bg-slate-950 sm:px-6 lg:px-10">
    <div class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.28em] text-emerald-500">Finance mutualisée</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Pool financier sécurisé</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">Chaque contribution payée et chaque transfert entre projets laisse une écriture vérifiable dans une chaîne SHA-256.</p>
            </div>
            <button wire:click="verifyLedger" wire:loading.attr="disabled" class="rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-xs font-black text-emerald-700 shadow-sm transition hover:bg-emerald-50 disabled:opacity-50 dark:border-emerald-300/20 dark:bg-slate-900 dark:text-emerald-200 dark:hover:bg-emerald-400/10">Vérifier l’intégrité</button>
        </div>

        @if (session('finance-message'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-300/20 dark:bg-emerald-400/10 dark:text-emerald-200">{{ session('finance-message') }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-[1.75rem] border border-emerald-200/70 bg-white p-5 shadow-sm dark:border-emerald-300/10 dark:bg-slate-900/80"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Fonds enregistrés</p><p class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($summary['funded'], 0, ',', ' ') }} <span class="text-sm text-slate-400">XOF</span></p></div>
            <div class="rounded-[1.75rem] border border-indigo-200/70 bg-white p-5 shadow-sm dark:border-indigo-300/10 dark:bg-slate-900/80"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Transferts internes</p><p class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($summary['transferred'], 0, ',', ' ') }} <span class="text-sm text-slate-400">XOF</span></p></div>
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-slate-900/80"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Écritures scellées</p><p class="mt-3 text-2xl font-black text-slate-900 dark:text-white">{{ $summary['entries'] }}</p></div>
        </div>

        <section class="rounded-[2rem] border border-emerald-200/70 bg-white p-5 shadow-xl shadow-emerald-950/5 dark:border-emerald-300/10 dark:bg-slate-900/80 sm:p-7">
            <div class="mb-6"><h2 class="text-lg font-black text-slate-900 dark:text-white">Transférer une ressource entre projets</h2><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Le solde est verrouillé et contrôlé dans la même transaction que l’écriture comptable.</p></div>
            <form wire:submit="transfer" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div><label class="mb-2 block text-xs font-bold text-slate-600 dark:text-slate-300">Projet source</label><select wire:model.live="sourceProjectId" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-emerald-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"><option value="">Choisir</option>@foreach ($projects as $project)<option value="{{ $project->id }}">{{ $project->titre }} — {{ number_format($projectBalances[$project->id], 0, ',', ' ') }} XOF</option>@endforeach</select>@error('sourceProjectId')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                <div><label class="mb-2 block text-xs font-bold text-slate-600 dark:text-slate-300">Projet destinataire</label><select wire:model.live="destinationProjectId" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-emerald-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"><option value="">Choisir</option>@foreach ($projects as $project)<option value="{{ $project->id }}">{{ $project->titre }}</option>@endforeach</select>@error('destinationProjectId')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                <div><label class="mb-2 block text-xs font-bold text-slate-600 dark:text-slate-300">Montant (XOF)</label><input wire:model.live="amount" type="number" min="0.01" step="0.01" placeholder="250000" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-emerald-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">@error('amount')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                <div><label class="mb-2 block text-xs font-bold text-slate-600 dark:text-slate-300">Référence</label><input wire:model.live="reference" type="text" placeholder="TRANSFERT-2026-001" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-emerald-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">@error('reference')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</div>
                <div class="md:col-span-2 xl:col-span-4"><button wire:loading.attr="disabled" class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-500 disabled:opacity-50">Sceller le transfert</button></div>
            </form>
        </section>

        <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900/80">
            <div class="border-b border-slate-200 px-5 py-5 dark:border-white/10 sm:px-7"><h2 class="text-lg font-black text-slate-900 dark:text-white">Registre des mouvements</h2></div>
            <div class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($ledger as $entry)
                    <div class="flex flex-col gap-3 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7"><div><p class="text-sm font-black text-slate-900 dark:text-white">{{ $entry->entry_type === 'transfer' ? 'Transfert interne' : 'Contribution payée' }}</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $entry->sourceProject?->titre ?: 'Hors projet' }} → {{ $entry->destinationProject?->titre ?: 'Pool' }} · {{ $entry->reference }}</p></div><div class="text-left sm:text-right"><p class="text-sm font-black text-emerald-600 dark:text-emerald-300">{{ number_format((float) $entry->amount, 0, ',', ' ') }} XOF</p><p class="mt-1 font-mono text-[10px] text-slate-400">{{ $entry->enregistre_le?->format('d/m/Y H:i') }}</p></div></div>
                @empty
                    <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">Aucune écriture financière pour le moment.</div>
                @endforelse
            </div>
            <div class="border-t border-slate-100 px-5 py-4 dark:border-white/5 sm:px-7">{{ $ledger->links() }}</div>
        </section>
    </div>
</div>
