<div class="min-h-screen bg-slate-950 text-slate-100">
    @if(session('project-message'))
        <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm font-semibold text-emerald-200">{{ session('project-message') }}</div>
        </div>
    @endif
    <div class="relative isolate overflow-hidden">
        <div class="pointer-events-none absolute -left-40 top-0 -z-10 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl"></div>
        <div class="pointer-events-none absolute right-0 top-40 -z-10 h-80 w-80 rounded-full bg-fuchsia-600/10 blur-3xl"></div>

        <section class="mx-auto max-w-7xl px-4 pb-8 pt-12 sm:px-6 lg:px-8 lg:pt-16">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-indigo-400/20 bg-indigo-400/10 px-3 py-1.5 text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-300">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-400"></span>
                        Mutualisation RIGO
                    </div>
                    <h1 class="text-4xl font-black tracking-tight text-white sm:text-5xl">
                        Des projets qui avancent
                        <span class="bg-gradient-to-r from-indigo-300 via-fuchsia-300 to-emerald-300 bg-clip-text text-transparent">ensemble.</span>
                    </h1>
                    <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-400 sm:text-base">
                        Explorez les initiatives ouvertes et trouvez la contribution qui fera la différence : financement, expertise ou équipement.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:flex sm:items-center">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.06] px-5 py-4 backdrop-blur-xl">
                        <div class="text-2xl font-black text-white">{{ $projects->total() }}</div>
                        <div class="mt-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">Projets visibles</div>
                    </div>
                    <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/[0.06] px-5 py-4 backdrop-blur-xl">
                        <div class="text-2xl font-black text-emerald-300">{{ $projects->currentPage() }}</div>
                        <div class="mt-1 text-[10px] font-bold uppercase tracking-widest text-emerald-300/60">Page actuelle</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-white/10 bg-white/[0.055] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center">
                    <label class="group relative block flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-500 transition-colors group-focus-within:text-indigo-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z" /></svg>
                        </span>
                        <input wire:model.live.debounce.350ms="search" type="search" placeholder="Rechercher un projet, une catégorie..." class="w-full rounded-2xl border border-white/10 bg-slate-950/60 py-3.5 pl-12 pr-4 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10">
                    </label>

                    <div class="grid grid-cols-2 gap-3 sm:flex">
                        <select wire:model.live="needType" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3.5 text-sm text-slate-300 outline-none transition focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10">
                            <option value="all">Tous les besoins</option>
                            <option value="financier">Financier</option>
                            <option value="competence">Compétences</option>
                            <option value="materiel">Matériel</option>
                        </select>
                        <select wire:model.live="status" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3.5 text-sm text-slate-300 outline-none transition focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10">
                            <option value="all">Tous les statuts</option>
                            @foreach($statuses as $projectStatus)
                                <option value="{{ $projectStatus->value }}">{{ $projectStatus->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($search !== '' || $needType !== 'all' || $status !== 'all')
                    <div class="mt-4 flex items-center justify-between border-t border-white/10 pt-4 text-xs">
                        <span class="text-slate-500">Filtres actifs · mise à jour instantanée</span>
                        <button wire:click="clearFilters" type="button" class="font-bold text-indigo-300 transition hover:text-white">Réinitialiser</button>
                    </div>
                @endif
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
            <div wire:loading.class="opacity-50" wire:target="search,needType,status,gotoPage,nextPage,previousPage" class="transition-opacity duration-200">
                @if($projects->isNotEmpty())
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($projects as $project)
                            @php
                                $isCompany = $project->user?->isPersonneMorale() ?? false;
                                $profile = $project->user?->profile;
                                $progress = $progressions[$project->id] ?? ['financial' => 0, 'human' => 0];
                                $skills = collect($project->besoins_competences ?? [])->map(fn ($item) => is_array($item) ? ($item['role'] ?? $item['label'] ?? $item['name'] ?? null) : $item)->filter()->take(3);
                                $materials = collect($project->besoins_materiels ?? [])->map(fn ($item) => is_array($item) ? ($item['label'] ?? $item['name'] ?? null) : $item)->filter()->take(3);
                            @endphp
                            <article wire:key="project-{{ $project->id }}" class="group relative flex min-h-[490px] flex-col overflow-hidden rounded-3xl border border-white/10 bg-white/[0.055] shadow-xl shadow-black/10 backdrop-blur-xl transition duration-300 hover:-translate-y-1 hover:border-indigo-400/40 hover:bg-white/[0.08] hover:shadow-indigo-950/40">
                                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-400/80 to-transparent opacity-70"></div>
                                <div class="flex items-center justify-between px-6 pt-6">
                                    <span class="rounded-full border border-indigo-300/20 bg-indigo-400/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-indigo-300">
                                        {{ $project->categorie ?: 'Projet RIGO' }}
                                    </span>
                                    <x-ui.badge :color="$project->statut?->color() ?? 'slate'" :label="$project->statut?->label() ?? 'Brouillon'" />
                                </div>

                                <div class="flex-1 px-6 pb-5 pt-5">
                                    <h2 class="text-xl font-extrabold leading-tight text-white transition-colors group-hover:text-indigo-200">{{ $project->titre }}</h2>
                                    <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-400">{{ $project->description }}</p>

                                    <div class="mt-5 flex flex-wrap gap-2">
                                        @foreach($skills as $skill)
                                            <span class="rounded-lg border border-fuchsia-300/15 bg-fuchsia-400/10 px-2.5 py-1 text-[10px] font-semibold text-fuchsia-200">{{ $skill }}</span>
                                        @endforeach
                                        @foreach($materials as $material)
                                            <span class="rounded-lg border border-amber-300/15 bg-amber-400/10 px-2.5 py-1 text-[10px] font-semibold text-amber-200">{{ $material }}</span>
                                        @endforeach
                                        @if($project->besoin_financier_target > 0 || $project->budget > 0)
                                            <span class="rounded-lg border border-emerald-300/15 bg-emerald-400/10 px-2.5 py-1 text-[10px] font-semibold text-emerald-200">Financement</span>
                                        @endif
                                    </div>

                                    <div class="mt-7 space-y-5">
                                        <div>
                                            <div class="mb-2 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider">
                                                <span class="flex items-center gap-2 text-emerald-300"><span class="h-2 w-2 rounded-full bg-emerald-400"></span>Progression financière</span>
                                                <span class="text-white">{{ number_format($progress['financial'], 0) }}%</span>
                                            </div>
                                            <div class="h-2 overflow-hidden rounded-full bg-slate-800"><div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-300 transition-all duration-700" style="width: {{ $progress['financial'] }}%"></div></div>
                                            <div class="mt-2 text-[10px] text-slate-500">Objectif : {{ number_format((float) ($project->besoin_financier_target ?: $project->budget), 0, ',', ' ') }} FCFA</div>
                                        </div>
                                        <div>
                                            <div class="mb-2 flex items-center justify-between text-[11px] font-bold uppercase tracking-wider">
                                                <span class="flex items-center gap-2 text-fuchsia-300"><span class="h-2 w-2 rounded-full bg-fuchsia-400"></span>Compétences mutualisées</span>
                                                <span class="text-white">{{ number_format($progress['human'], 0) }}%</span>
                                            </div>
                                            <div class="h-2 overflow-hidden rounded-full bg-slate-800"><div class="h-full rounded-full bg-gradient-to-r from-fuchsia-500 to-indigo-300 transition-all duration-700" style="width: {{ $progress['human'] }}%"></div></div>
                                            <div class="mt-2 text-[10px] text-slate-500">{{ $skills->count() }} profil(s) recherché(s)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between border-t border-white/10 bg-black/10 px-6 py-4">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $isCompany ? 'bg-amber-400/15 text-amber-300' : 'bg-indigo-400/15 text-indigo-300' }}">
                                            @if($isCompany)
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V5l7-2 7 2v16M9 8h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1" /></svg>
                                            @else
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19a4 4 0 0 0-8 0m4-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm8 8a3 3 0 0 0-5.5-1.7M17 11a2.5 2.5 0 1 0-1.5-4.5" /></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 truncate text-xs font-bold text-slate-200">{{ $project->user?->name ?? 'Porteur RIGO' }} @if($profile?->is_verified)<svg class="h-3.5 w-3.5 shrink-0 text-sky-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.857a.75.75 0 0 0-1.06-1.06L9 10.879 7.203 9.08a.75.75 0 1 0-1.06 1.061l2.327 2.327a.75.75 0 0 0 1.06 0l4.327-4.326Z" clip-rule="evenodd" /></svg>@endif</div>
                                            <div class="text-[10px] text-slate-500">{{ $isCompany ? 'Personne morale' : 'Personne physique' }}</div>
                                        </div>
                                    </div>
                                    @auth
                                        <div class="flex items-center gap-2"><a href="{{ route('projects.show', $project) }}" wire:navigate class="rounded-xl border border-white/10 px-3 py-2 text-[10px] font-bold text-slate-300 transition hover:border-white/20 hover:bg-white/10 hover:text-white">Détails</a><button wire:click="$dispatch('open-contribution-modal', { projectId: {{ $project->id }} })" type="button" class="rounded-xl border border-white/10 px-3 py-2 text-[10px] font-bold text-slate-300 transition hover:border-indigo-300/40 hover:bg-indigo-400/10 hover:text-white">Contribuer <span class="ml-1">→</span></button></div>
                                    @else
                                        <div class="flex items-center gap-2"><a href="{{ route('projects.show', $project) }}" wire:navigate class="rounded-xl border border-white/10 px-3 py-2 text-[10px] font-bold text-slate-300 transition hover:border-white/20 hover:bg-white/10 hover:text-white">Détails</a><a href="{{ route('login') }}" wire:navigate class="rounded-xl border border-white/10 px-3 py-2 text-[10px] font-bold text-slate-300 transition hover:border-indigo-300/40 hover:bg-indigo-400/10 hover:text-white">Se connecter <span class="ml-1">→</span></a></div>
                                    @endauth
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <x-ui.empty-state
                        class="bg-white/[0.03]"
                        heading="Aucun projet trouvé"
                        text="Essayez une autre recherche ou réinitialisez vos filtres pour explorer le catalogue."
                    >
                        <x-slot:icon>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z" /></svg>
                        </x-slot:icon>
                        <button wire:click="clearFilters" type="button" class="mt-5 rounded-xl bg-indigo-500 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-400">Réinitialiser les filtres</button>
                    </x-ui.empty-state>
                @endif
            </div>

            @if($projects->hasPages())
                <div class="mt-10 border-t border-white/10 pt-6 [&_nav]:flex [&_nav]:items-center [&_nav]:justify-between [&_nav_span]:border-white/10 [&_nav_span]:bg-white/[0.05] [&_nav_span]:text-slate-500 [&_nav_a]:border-white/10 [&_nav_a]:bg-white/[0.05] [&_nav_a]:text-slate-300 [&_nav_a]:transition [&_nav_a:hover]:border-indigo-400/40 [&_nav_a:hover]:bg-indigo-400/10">
                    {{ $projects->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
