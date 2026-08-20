<div class="min-h-screen bg-slate-950 text-slate-100">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        @if(session('review-message'))
            <div class="mb-6 rounded-2xl border border-indigo-400/20 bg-indigo-400/10 px-4 py-3 text-sm font-semibold text-indigo-200">{{ session('review-message') }}</div>
        @endif

        <x-ui.card glow>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-300">Comité de pilotage</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">Revue des projets</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-400">Orientez les projets étudiés vers la mutualisation active ou clôturez les initiatives terminées.</p>
                </div>
                <span class="rounded-2xl border border-indigo-400/20 bg-indigo-400/10 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-indigo-200">Accès Chef de Projet · Top Management</span>
            </div>
        </x-ui.card>

        <div class="mt-6 overflow-hidden rounded-3xl border border-white/10 bg-white/[0.045] shadow-xl">
            <div class="hidden grid-cols-[1.5fr_1fr_0.9fr_auto] gap-4 border-b border-white/10 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 md:grid">
                <span>Projet</span>
                <span>Porteur</span>
                <span>Statut</span>
                <span>Action</span>
            </div>
            <div class="divide-y divide-white/10">
                @forelse($projects as $project)
                    <div wire:key="review-{{ $project->id }}" class="grid gap-4 px-5 py-5 md:grid-cols-[1.5fr_1fr_0.9fr_auto] md:items-center md:px-6">
                        <div>
                            <p class="text-sm font-black text-white">{{ $project->titre }}</p>
                            <p class="mt-1 line-clamp-1 text-xs text-slate-500">{{ $project->description }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-300">{{ $project->user?->name ?? 'Porteur inconnu' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $project->categorie }}</p>
                        </div>
                        <x-ui.badge :color="$project->statut->color()" :label="$project->statut->label()" />
                        <div>
                            @if($project->statut !== \App\Enums\ProjectStatus::CLOTURE)
                                <button wire:click="openTransition({{ $project->id }})" type="button" class="rounded-xl bg-indigo-500/15 px-3 py-2 text-[10px] font-black text-indigo-300 transition hover:bg-indigo-500 hover:text-white">Changer le statut</button>
                            @else
                                <span class="text-[10px] font-bold text-slate-600">Dossier clôturé</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state :bordered="false" heading="Aucun projet à revoir" text="Les projets en étude apparaîtront ici." />
                @endforelse
            </div>
        </div>

        @if($projects->hasPages())
            <div class="mt-6">{{ $projects->links() }}</div>
        @endif
    </div>

    <x-ui.modal wireProperty="showTransition" onClose="closeTransition" title="Faire évoluer le projet">
        <x-slot:header>
            <p class="text-[10px] font-black uppercase tracking-widest text-indigo-300">Décision de pilotage</p>
            <h2 class="mt-2 text-xl font-black text-white">Faire évoluer le projet</h2>
        </x-slot:header>

        <form wire:submit="updateStatus" class="space-y-5">
            <label class="block">
                <span class="mb-2 block text-xs font-bold text-slate-300">Nouveau statut</span>
                <select wire:model.live="targetStatus" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10">
                    <option value="en_cours_de_mutualisation">En cours de mutualisation</option>
                    <option value="cloture">Clôturer le projet</option>
                </select>
                @error('targetStatus')
                    <span class="mt-2 block text-xs text-rose-400">{{ $message }}</span>
                @enderror
            </label>
            <button type="submit" class="w-full rounded-2xl bg-indigo-500 px-5 py-3.5 text-sm font-black text-white transition hover:bg-indigo-400">Appliquer la décision</button>
        </form>
    </x-ui.modal>
</div>
