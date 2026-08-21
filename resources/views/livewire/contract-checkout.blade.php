<div class="mx-auto max-w-3xl">
    <div class="relative overflow-hidden rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.045] p-6 shadow-sm dark:shadow-2xl backdrop-blur-xl dark:backdrop-blur-2xl sm:p-8">
        <div class="pointer-events-none absolute -right-20 -top-32 h-72 w-72 rounded-full bg-indigo-400/10 dark:bg-indigo-500/15 blur-3xl"></div>

        <div class="relative flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-300">Contrat de mutualisation</p>
                <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl">{{ $contract->project?->titre ?? 'Projet' }}</h1>
                <p class="mt-2 max-w-xl text-sm leading-6 text-slate-500 dark:text-slate-400">Formalisez et payez votre apport financier validé pour ce projet.</p>
            </div>
            <span class="w-fit rounded-full border border-indigo-200 dark:border-indigo-400/20 bg-indigo-50 dark:bg-indigo-400/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-300">
                {{ $contract->contract_number }}
            </span>
        </div>

        <div class="relative mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach(['Signature', 'Paiement', 'Activation'] as $index => $label)
                @php
                    $isDone = match ($index) {
                        0 => $contract->terms_accepted_at !== null,
                        1 => $contract->status === $contractStatus::ACTIVE,
                        default => $contract->status === $contractStatus::ACTIVE,
                    };
                @endphp
                <div class="rounded-2xl border px-3 py-2.5 text-center text-[10px] font-black uppercase tracking-wider {{ $isDone ? 'border-emerald-300 dark:border-emerald-400/30 bg-emerald-50 dark:bg-emerald-400/10 text-emerald-600 dark:text-emerald-300' : 'border-slate-200 dark:border-white/10 bg-white/60 dark:bg-white/[0.03] text-slate-500' }}">
                    {{ $label }}
                </div>
            @endforeach
        </div>

        <div class="relative mt-8 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-black/10 p-5 sm:p-6">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Clauses du contrat</p>
            <dl class="mt-4 space-y-4 text-sm">
                <div>
                    <dt class="font-bold text-slate-700 dark:text-slate-300">1. Identité des parties</dt>
                    <dd class="mt-1 text-slate-500 dark:text-slate-400">
                        Porteur de projet : <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $contract->project?->user?->name ?? '—' }}</span><br>
                        Contributeur : <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $contract->user->name }}</span>
                    </dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-700 dark:text-slate-300">2. Objet de la mutualisation</dt>
                    <dd class="mt-1 text-slate-500 dark:text-slate-400">{{ $contract->project?->description ?? 'Description non renseignée.' }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-700 dark:text-slate-300">3. Montant et modalités</dt>
                    <dd class="mt-1 text-slate-500 dark:text-slate-400">
                        Apport financier de <span class="font-black text-slate-900 dark:text-white">{{ number_format((float) $contract->amount, 0, ',', ' ') }} {{ $contract->currency }}</span>, réglé en une fois via le moyen de paiement choisi ci-dessous.
                    </dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-700 dark:text-slate-300">4. Durée & conditions générales</dt>
                    <dd class="mt-1 text-slate-500 dark:text-slate-400">Le contrat prend effet à la confirmation du paiement et engage les deux parties pour la durée du projet de mutualisation.</dd>
                </div>
            </dl>
        </div>

        @if($contract->terms_accepted_at === null)
            {{-- Étape 1 : signature --}}
            <form wire:submit="signContract" class="relative mt-6 space-y-4">
                <label class="flex items-start gap-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-white/60 dark:bg-white/[0.03] px-4 py-4">
                    <input type="checkbox" wire:model="accepted" class="mt-0.5 h-4 w-4 rounded border-slate-300 dark:border-white/20 bg-white dark:bg-slate-900 text-indigo-500 focus:ring-indigo-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">J'ai lu et j'accepte les clauses du contrat ci-dessus. Ma signature électronique sera horodatée et associée à mon adresse IP.</span>
                </label>
                @error('accepted') <p class="text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p> @enderror

                <button type="submit" wire:loading.attr="disabled" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-5 py-4 text-sm font-black text-white shadow-xl shadow-indigo-950/40 transition hover:from-indigo-400 hover:to-fuchsia-400 disabled:cursor-wait disabled:opacity-60">
                    <span wire:loading.remove wire:target="signContract">Signer le contrat <span aria-hidden="true">→</span></span>
                    <span wire:loading wire:target="signContract">Signature en cours...</span>
                </button>
            </form>
        @elseif($contract->status !== $contractStatus::ACTIVE)
            {{-- Étape 2 : paiement --}}
            <div class="relative mt-6 space-y-4">
                <div class="rounded-2xl border border-emerald-200 dark:border-emerald-400/20 bg-emerald-50 dark:bg-emerald-400/10 px-4 py-3 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                    Contrat signé le {{ $contract->signed_at->format('d/m/Y à H:i') }}.
                </div>

                @if($paymentFailed)
                    <div class="rounded-2xl border border-rose-200 dark:border-rose-400/20 bg-rose-50 dark:bg-rose-400/10 px-4 py-3 text-xs font-semibold text-rose-700 dark:text-rose-300">
                        Le dernier paiement a échoué{{ $latestPayment?->failed_reason ? " : {$latestPayment->failed_reason}" : '.' }}
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <button wire:click="$set('paymentMethod', 'mobile_money')" type="button" class="rounded-2xl border px-4 py-4 text-left transition {{ $paymentMethod === 'mobile_money' ? 'border-emerald-300 dark:border-emerald-400/50 bg-emerald-50 dark:bg-emerald-400/10 text-emerald-700 dark:text-emerald-200' : 'border-slate-200 dark:border-white/10 bg-white/60 dark:bg-white/[0.04] text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-white/20 hover:text-slate-900 dark:hover:text-white' }}">
                        <span class="block text-xs font-bold">Mobile Money</span>
                        <span class="mt-1 block text-[10px] opacity-60">Orange Money, Moov Money...</span>
                    </button>
                    <button wire:click="$set('paymentMethod', 'carte')" type="button" class="rounded-2xl border px-4 py-4 text-left transition {{ $paymentMethod === 'carte' ? 'border-indigo-300 dark:border-indigo-400/50 bg-indigo-50 dark:bg-indigo-400/10 text-indigo-700 dark:text-indigo-200' : 'border-slate-200 dark:border-white/10 bg-white/60 dark:bg-white/[0.04] text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-white/20 hover:text-slate-900 dark:hover:text-white' }}">
                        <span class="block text-xs font-bold">Carte bancaire</span>
                        <span class="mt-1 block text-[10px] opacity-60">Visa, Mastercard</span>
                    </button>
                </div>

                <button wire:click="pay('{{ $paymentMethod }}')" wire:loading.attr="disabled" wire:target="pay" type="button" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-5 py-4 text-sm font-black text-white shadow-xl shadow-indigo-950/40 transition hover:from-indigo-400 hover:to-fuchsia-400 disabled:cursor-wait disabled:opacity-60">
                    <span wire:loading.remove wire:target="pay">
                        {{ $paymentFailed ? 'Réessayer le paiement' : 'Payer' }} {{ number_format((float) $contract->amount, 0, ',', ' ') }} {{ $contract->currency }} <span aria-hidden="true">→</span>
                    </span>
                    <span wire:loading wire:target="pay">Traitement du paiement...</span>
                </button>
            </div>
        @else
            {{-- Étape 3 : succès --}}
            <div class="relative mt-6 space-y-4 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                </div>
                <p class="text-lg font-black text-slate-900 dark:text-white">Contrat actif et payé</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">Empreinte de sécurité : <span class="font-mono text-slate-700 dark:text-slate-300">{{ substr((string) $contract->document_hash, 0, 16) }}…</span></p>

                <a href="{{ route('contracts.download', $contract) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-6 py-4 text-sm font-black text-white shadow-xl shadow-indigo-950/40 transition hover:from-indigo-400 hover:to-fuchsia-400">
                    Télécharger le contrat (PDF)
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13m0 0 4-4m-4 4-4-4M5 21h14" /></svg>
                </a>
            </div>
        @endif
    </div>
</div>
