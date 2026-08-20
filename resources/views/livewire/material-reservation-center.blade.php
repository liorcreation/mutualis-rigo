<div class="min-h-screen bg-slate-950 text-slate-100">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        @if(session('reservation-message'))
            <div class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm font-semibold text-emerald-200">{{ session('reservation-message') }}</div>
        @endif

        <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-white/[0.055] p-6 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-8">
            <div class="pointer-events-none absolute -right-20 -top-32 h-72 w-72 rounded-full bg-indigo-500/15 blur-3xl"></div>
            <div class="relative">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-300">Mutualisation matérielle</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">Réservation de matériel</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-400">Empruntez le matériel validé d'un autre projet ou gérez les demandes reçues sur vos propres apports.</p>
            </div>
        </div>

        <div class="mt-10 flex gap-2 overflow-x-auto border-b border-white/10">
            <button wire:click="selectTab('catalogue')" type="button" class="whitespace-nowrap border-b-2 px-4 py-4 text-xs font-black transition {{ $activeTab === 'catalogue' ? 'border-indigo-400 text-indigo-300' : 'border-transparent text-slate-500 hover:text-white' }}">Matériel disponible</button>
            <button wire:click="selectTab('mes-demandes')" type="button" class="whitespace-nowrap border-b-2 px-4 py-4 text-xs font-black transition {{ $activeTab === 'mes-demandes' ? 'border-indigo-400 text-indigo-300' : 'border-transparent text-slate-500 hover:text-white' }}">Mes demandes</button>
            <button wire:click="selectTab('demandes-recues')" type="button" class="whitespace-nowrap border-b-2 px-4 py-4 text-xs font-black transition {{ $activeTab === 'demandes-recues' ? 'border-indigo-400 text-indigo-300' : 'border-transparent text-slate-500 hover:text-white' }}">Demandes reçues</button>
        </div>

        <div class="mt-6" wire:loading.class="opacity-50" wire:target="selectTab,gotoPage,nextPage,previousPage">

            {{-- Catalogue : matériel disponible à réserver --}}
            @if($activeTab === 'catalogue')
                @if($availableMaterials->isNotEmpty())
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($availableMaterials as $material)
                            <article wire:key="material-{{ $material->id }}" class="flex flex-col justify-between rounded-3xl border border-white/10 bg-white/[0.045] p-5 transition hover:-translate-y-1 hover:border-indigo-400/30">
                                <div>
                                    <span class="rounded-full border border-amber-400/20 bg-amber-400/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-300">Matériel</span>
                                    <h2 class="mt-4 text-base font-black text-white">{{ $material->project?->titre ?? 'Projet supprimé' }}</h2>
                                    <p class="mt-2 line-clamp-3 text-xs leading-5 text-slate-500">{{ $material->description_apport ?: 'Aucun détail fourni' }}</p>
                                    <p class="mt-3 text-[11px] font-semibold text-slate-400">Proposé par {{ $material->user?->name ?? 'Utilisateur' }}</p>
                                </div>
                                <button wire:click="openRequestForm({{ $material->id }})" type="button" class="mt-5 w-full rounded-xl bg-indigo-500/15 px-3 py-2.5 text-[11px] font-black text-indigo-300 transition hover:bg-indigo-500 hover:text-white">Demander une réservation</button>
                            </article>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state heading="Aucun matériel disponible pour l'instant" text="Les apports matériels validés d'autres projets apparaîtront ici." />
                @endif
                @if($availableMaterials->hasPages())<div class="mt-6">{{ $availableMaterials->links() }}</div>@endif
            @endif

            {{-- Mes demandes --}}
            @if($activeTab === 'mes-demandes')
                @if($myRequests->isNotEmpty())
                    <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045]">
                        <div class="divide-y divide-white/10">
                            @foreach($myRequests as $reservation)
                                <div wire:key="my-reservation-{{ $reservation->id }}" class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-black text-white">{{ $reservation->contribution?->project?->titre ?? 'Projet supprimé' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Du {{ $reservation->date_debut->format('d/m/Y') }} au {{ $reservation->date_fin->format('d/m/Y') }} · Qté {{ $reservation->quantite }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Contributeur : {{ $reservation->contribution?->user?->name ?? 'Utilisateur' }}</p>
                                        @if($reservation->commentaire_validation)
                                            <p class="mt-2 text-xs italic text-slate-400">« {{ $reservation->commentaire_validation }} »</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-ui.badge :color="$reservation->statut->color()" :label="$reservation->statut->label()" />
                                        @if($reservation->statut === \App\Enums\ReservationStatus::EN_ATTENTE)
                                            <button wire:click="cancel({{ $reservation->id }})" wire:confirm="Annuler cette demande de réservation ?" type="button" class="w-fit rounded-xl bg-white/10 px-3 py-2 text-[10px] font-black text-slate-300 transition hover:bg-white/20 hover:text-white">Annuler</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <x-ui.empty-state heading="Aucune demande envoyée" text="Parcourez le matériel disponible pour envoyer votre première demande." />
                @endif
                @if($myRequests->hasPages())<div class="mt-6">{{ $myRequests->links() }}</div>@endif
            @endif

            {{-- Demandes reçues sur mon matériel --}}
            @if($activeTab === 'demandes-recues')
                @if($receivedRequests->isNotEmpty())
                    <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045]">
                        <div class="divide-y divide-white/10">
                            @foreach($receivedRequests as $reservation)
                                <div wire:key="received-reservation-{{ $reservation->id }}" class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-black text-white">{{ $reservation->requester?->name ?? 'Utilisateur' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $reservation->contribution?->project?->titre ?? 'Projet supprimé' }} · Du {{ $reservation->date_debut->format('d/m/Y') }} au {{ $reservation->date_fin->format('d/m/Y') }} · Qté {{ $reservation->quantite }}</p>
                                        @if($reservation->commentaire)
                                            <p class="mt-2 text-xs italic text-slate-400">« {{ $reservation->commentaire }} »</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($reservation->statut === \App\Enums\ReservationStatus::EN_ATTENTE)
                                            <button wire:click="review({{ $reservation->id }}, 'validee')" type="button" class="rounded-xl bg-emerald-500/15 px-3 py-2 text-[10px] font-black text-emerald-300 transition hover:bg-emerald-500 hover:text-white">Valider</button>
                                            <button wire:click="review({{ $reservation->id }}, 'refusee')" type="button" class="rounded-xl bg-rose-500/15 px-3 py-2 text-[10px] font-black text-rose-300 transition hover:bg-rose-500 hover:text-white">Refuser</button>
                                        @else
                                            <x-ui.badge :color="$reservation->statut->color()" :label="$reservation->statut->label()" />
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <x-ui.empty-state heading="Aucune demande reçue" text="Les demandes sur votre matériel mutualisé apparaîtront ici." />
                @endif
                @if($receivedRequests->hasPages())<div class="mt-6">{{ $receivedRequests->links() }}</div>@endif
            @endif
        </div>
    </div>

    {{-- Formulaire de demande de réservation --}}
    <x-ui.modal wireProperty="showRequestForm" onClose="closeRequestForm">
        <x-slot:header>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-300">Demande de réservation</p>
            <h2 class="mt-2 text-2xl font-black tracking-tight text-white">Choisissez votre créneau</h2>
            <p class="mt-2 text-sm text-slate-400">Le contributeur devra valider votre demande.</p>
        </x-slot:header>

        <form wire:submit="submitRequest" class="space-y-5">
            <div class="grid grid-cols-2 gap-3">
                <label class="block">
                    <span class="mb-2 block text-xs font-bold text-slate-300">Du <span class="text-rose-400">*</span></span>
                    <input wire:model.live="dateDebut" type="date" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none transition focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('dateDebut') border-rose-400/70 @enderror">
                    @error('dateDebut') <span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs font-bold text-slate-300">Au <span class="text-rose-400">*</span></span>
                    <input wire:model.live="dateFin" type="date" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none transition focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('dateFin') border-rose-400/70 @enderror">
                    @error('dateFin') <span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span> @enderror
                </label>
            </div>

            @if($this->availability === false)
                <div class="rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-xs font-semibold text-rose-200">Ce matériel n'est pas disponible sur cette période : une autre demande est déjà en attente ou validée.</div>
            @elseif($this->availability === true)
                <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-xs font-semibold text-emerald-200">Ce matériel est disponible sur la période sélectionnée.</div>
            @endif

            <label class="block">
                <span class="mb-2 block text-xs font-bold text-slate-300">Quantité <span class="text-rose-400">*</span></span>
                <input wire:model.live.debounce.250ms="quantite" type="number" min="1" step="1" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none transition focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('quantite') border-rose-400/70 @enderror">
                @error('quantite') <span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="mb-2 block text-xs font-bold text-slate-300">Message <span class="font-normal text-slate-600">(optionnel)</span></span>
                <textarea wire:model.live.debounce.250ms="commentaire" rows="3" placeholder="Précisez l'usage prévu, le lieu de retrait..." class="w-full resize-none rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('commentaire') border-rose-400/70 @enderror"></textarea>
                @error('commentaire') <span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span> @enderror
            </label>

            <button type="submit" wire:loading.attr="disabled" wire:target="submitRequest" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-5 py-4 text-sm font-black text-white shadow-xl shadow-indigo-950/40 transition hover:from-indigo-400 hover:to-fuchsia-400 disabled:cursor-wait disabled:opacity-60">
                <span wire:loading.remove wire:target="submitRequest">Envoyer ma demande <span aria-hidden="true">→</span></span>
                <span wire:loading wire:target="submitRequest">Envoi en cours...</span>
            </button>
        </form>
    </x-ui.modal>

    {{-- Panneau de décision (contributeur) --}}
    <x-ui.modal wireProperty="showReview" onClose="closeReview" :accent="$decision === 'validee' ? 'bg-emerald-400' : 'bg-rose-400'">
        <x-slot:header>
            <p class="text-[10px] font-black uppercase tracking-widest {{ $decision === 'validee' ? 'text-emerald-300' : 'text-rose-300' }}">{{ $decision === 'validee' ? 'Validation' : 'Refus' }}</p>
            <h2 class="mt-2 text-xl font-black text-white">Confirmer votre décision</h2>
        </x-slot:header>

        <form wire:submit="saveDecision" class="space-y-5">
            <label class="block">
                <span class="mb-2 block text-xs font-bold text-slate-300">Commentaire / motif @if($decision === 'refusee')<span class="text-rose-400">*</span>@else<span class="font-normal text-slate-600">(optionnel)</span>@endif</span>
                <textarea wire:model.live.debounce.250ms="commentaireValidation" rows="4" placeholder="Expliquez votre décision au demandeur..." class="w-full resize-none rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('commentaireValidation') border-rose-400/70 @enderror"></textarea>
                @error('commentaireValidation') <span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span> @enderror
            </label>
            <button wire:loading.attr="disabled" type="submit" class="w-full rounded-2xl {{ $decision === 'validee' ? 'bg-emerald-500 hover:bg-emerald-400' : 'bg-rose-500 hover:bg-rose-400' }} px-5 py-3.5 text-sm font-black text-white transition disabled:opacity-50">
                <span wire:loading.remove>Confirmer</span>
                <span wire:loading>Traitement...</span>
            </button>
        </form>
    </x-ui.modal>
</div>
