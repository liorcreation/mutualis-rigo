<div>
    <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-white/[0.055] p-6 shadow-2xl backdrop-blur-2xl sm:p-8">
        <div class="pointer-events-none absolute -right-20 -top-32 h-72 w-72 rounded-full bg-indigo-500/15 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-300">Historique</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">Mes paiements</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-400">Suivez vos transactions et retéléchargez vos contrats à tout moment.</p>
            </div>
        </div>

        <div class="relative mt-6 flex flex-col gap-3 sm:flex-row">
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher une référence ou un contrat..." class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 sm:max-w-sm">
            <select wire:model.live="statusFilter" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none transition focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 sm:w-56">
                <option value="all">Tous les statuts</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl">
        <div class="hidden grid-cols-[1fr_1fr_0.8fr_0.8fr_auto] gap-4 border-b border-white/10 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 md:grid">
            <span>Référence</span>
            <span>Contrat / Projet</span>
            <span>Montant</span>
            <span>Statut</span>
            <span>Action</span>
        </div>
        <div class="divide-y divide-white/10">
            @forelse($payments as $payment)
                <div wire:key="payment-{{ $payment->id }}" class="grid gap-4 px-5 py-5 md:grid-cols-[1fr_1fr_0.8fr_0.8fr_auto] md:items-center md:px-6">
                    <div>
                        <p class="font-mono text-sm font-black text-white">{{ $payment->reference }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $payment->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div>
                        <p class="font-mono text-sm font-bold text-slate-300">{{ $payment->contract?->contract_number ?? '—' }}</p>
                        <p class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $payment->contract?->project?->titre ?? 'Projet supprimé' }}</p>
                    </div>
                    <span class="text-sm font-black text-white">{{ number_format((float) $payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</span>
                    <x-ui.badge :color="$payment->status->color()" :label="$payment->status->label()" />
                    <div>
                        @if($payment->contract?->pdf_path)
                            <a href="{{ route('contracts.download', $payment->contract) }}" class="rounded-xl bg-indigo-500/15 px-3 py-2 text-[10px] font-black text-indigo-300 transition hover:bg-indigo-500 hover:text-white">
                                Télécharger le contrat
                            </a>
                        @else
                            <span class="text-[10px] font-semibold text-slate-600">Indisponible</span>
                        @endif
                    </div>
                </div>
            @empty
                <x-ui.empty-state :bordered="false" heading="Aucun paiement trouvé" text="Vos transactions apparaîtront ici dès qu'un contrat sera payé." />
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        {{ $payments->links() }}
    </div>
</div>
