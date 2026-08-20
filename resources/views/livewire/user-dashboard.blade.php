<div class="min-h-screen bg-slate-950 text-slate-100">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        @if(session('project-message'))
            <div class="mb-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm font-semibold text-emerald-200">{{ session('project-message') }}</div>
        @endif
        @if(session('contribution-message'))
            <div class="mb-6 rounded-2xl border border-indigo-400/20 bg-indigo-400/10 px-4 py-3 text-sm font-semibold text-indigo-200">{{ session('contribution-message') }}</div>
        @endif

        <x-ui.card glow>
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-300">Espace personnel</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">Bonjour, {{ auth()->user()->name }}.</h1>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-slate-400">Retrouvez vos projets, vos apports et les prochaines étapes de vos initiatives.</p>
                </div>
                <a href="{{ route('projects.create') }}" wire:navigate class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-5 py-3.5 text-xs font-black text-white shadow-lg shadow-indigo-950/40 transition hover:from-indigo-400 hover:to-fuchsia-400">+ Nouveau projet</a>
            </div>
        </x-ui.card>

        <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Mes projets</p>
                <p class="mt-2 text-3xl font-black text-white">{{ $projects->total() }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Mes apports</p>
                <p class="mt-2 text-3xl font-black text-white">{{ $contributions->total() }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-400/15 bg-emerald-400/[0.05] p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-300/60">Apports validés</p>
                <p class="mt-2 text-3xl font-black text-emerald-300">{{ $validatedContributions }}</p>
            </div>
            <div class="rounded-2xl border border-amber-400/15 bg-amber-400/[0.05] p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-amber-300/60">En attente</p>
                <p class="mt-2 text-3xl font-black text-amber-300">{{ $pendingContributions }}</p>
            </div>
        </div>

        <div class="mt-10 flex gap-2 overflow-x-auto border-b border-white/10">
            <button wire:click="selectTab('projects')" type="button" class="whitespace-nowrap border-b-2 px-4 py-4 text-xs font-black transition {{ $activeTab === 'projects' ? 'border-indigo-400 text-indigo-300' : 'border-transparent text-slate-500 hover:text-white' }}">Mes projets</button>
            <button wire:click="selectTab('contributions')" type="button" class="whitespace-nowrap border-b-2 px-4 py-4 text-xs font-black transition {{ $activeTab === 'contributions' ? 'border-indigo-400 text-indigo-300' : 'border-transparent text-slate-500 hover:text-white' }}">Mes contributions</button>
        </div>

        <div class="mt-6" wire:loading.class="opacity-50" wire:target="selectTab,gotoPage,nextPage,previousPage">
            @if($activeTab === 'projects')
                @if($projects->isNotEmpty())
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($projects as $project)
                            <article wire:key="my-project-{{ $project->id }}" class="rounded-3xl border border-white/10 bg-white/[0.045] p-5 transition hover:-translate-y-1 hover:border-indigo-400/30">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="rounded-full bg-indigo-400/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-indigo-300">{{ $project->categorie }}</span>
                                    <x-ui.badge :color="$project->statut->color()" :label="$project->statut->label()" />
                                </div>
                                <h2 class="mt-5 text-lg font-black text-white">{{ $project->titre }}</h2>
                                <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500">{{ $project->description }}</p>
                                <div class="mt-5 space-y-3">
                                    <div>
                                        <div class="mb-1 flex justify-between text-[10px] font-bold text-slate-400">
                                            <span>Financement</span>
                                            <span>{{ number_format($progressions[$project->id]['financial'] ?? 0, 0) }}%</span>
                                        </div>
                                        <div class="h-1.5 rounded-full bg-slate-800"><div class="h-full rounded-full bg-emerald-400" style="width: {{ $progressions[$project->id]['financial'] ?? 0 }}%"></div></div>
                                    </div>
                                    <div>
                                        <div class="mb-1 flex justify-between text-[10px] font-bold text-slate-400">
                                            <span>Compétences</span>
                                            <span>{{ number_format($progressions[$project->id]['human'] ?? 0, 0) }}%</span>
                                        </div>
                                        <div class="h-1.5 rounded-full bg-slate-800"><div class="h-full rounded-full bg-fuchsia-400" style="width: {{ $progressions[$project->id]['human'] ?? 0 }}%"></div></div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state heading="Aucun projet publié" text="Votre première initiative peut commencer maintenant." ctaLabel="Créer un projet" :ctaHref="route('projects.create')" />
                @endif
                @if($projects->hasPages())<div class="mt-6">{{ $projects->links() }}</div>@endif
            @else
                @if($contributions->isNotEmpty())
                    <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045]">
                        <div class="divide-y divide-white/10">
                            @foreach($contributions as $contribution)
                                @php
                                    $isFinancial = $contribution->type_apport->value === 'financier';
                                    $hasActiveContract = $contribution->contract?->status === \App\Enums\ContractStatus::ACTIVE;
                                    $badgeLabel = match(true) {
                                        $contribution->statut === \App\Enums\ContributionStatus::REFUSE => 'Refusé',
                                        $contribution->statut === \App\Enums\ContributionStatus::EN_ATTENTE => 'En attente de validation',
                                        $isFinancial && $hasActiveContract => 'Contrat actif',
                                        $isFinancial => 'En attente de signature/paiement',
                                        default => 'Validé',
                                    };
                                    $badgeColor = match(true) {
                                        $contribution->statut === \App\Enums\ContributionStatus::REFUSE => 'rose',
                                        $contribution->statut === \App\Enums\ContributionStatus::EN_ATTENTE => 'amber',
                                        $isFinancial && ! $hasActiveContract => 'amber',
                                        default => 'emerald',
                                    };
                                @endphp
                                <div wire:key="my-contribution-{{ $contribution->id }}" class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isFinancial ? 'bg-emerald-400/10 text-emerald-300' : 'bg-fuchsia-400/10 text-fuchsia-300' }}">{{ $isFinancial ? '₣' : '✦' }}</div>
                                        <div>
                                            <p class="text-sm font-black text-white">{{ $contribution->project?->titre ?? 'Projet supprimé' }}</p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $contribution->type_apport->label() }}
                                                @if($contribution->montant) · {{ number_format((float) $contribution->montant, 0, ',', ' ') }} FCFA @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-ui.badge :color="$badgeColor" :label="$badgeLabel" />
                                        @if($isFinancial && $contribution->statut === \App\Enums\ContributionStatus::VALIDE)
                                            <a href="{{ route('contracts.checkout', $contribution) }}" wire:navigate class="w-fit rounded-xl bg-indigo-500/15 px-3 py-2 text-[10px] font-black text-indigo-300 transition hover:bg-indigo-500 hover:text-white">{{ $hasActiveContract ? 'Voir le contrat' : 'Contractualiser & payer' }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <x-ui.empty-state heading="Aucune contribution" text="Explorez le catalogue pour soutenir un projet." ctaLabel="Explorer les projets" :ctaHref="route('projects.index')" />
                @endif
                @if($contributions->hasPages())<div class="mt-6">{{ $contributions->links() }}</div>@endif
            @endif
        </div>
    </div>
</div>
