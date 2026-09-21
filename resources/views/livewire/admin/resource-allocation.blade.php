<div class="min-h-screen bg-slate-50 px-4 py-8 dark:bg-slate-950 sm:px-6 lg:px-10">
    <div class="mx-auto max-w-7xl space-y-8">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.28em] text-fuchsia-500">Pilotage RH</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Matrice de charge</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">Affectez les compétences aux projets avec une règle simple et vérifiable : aucune personne ne peut dépasser 100 % de charge sur des périodes qui se chevauchent.</p>
        </div>

        @if (session('allocation-message'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-300/20 dark:bg-emerald-400/10 dark:text-emerald-200">{{ session('allocation-message') }}</div>
        @endif

        <section class="rounded-[2rem] border border-fuchsia-200/70 bg-white p-5 shadow-xl shadow-fuchsia-950/5 dark:border-fuchsia-300/10 dark:bg-slate-900/80 sm:p-7">
            <div class="mb-6 flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-fuchsia-100 text-fuchsia-600 dark:bg-fuchsia-400/10 dark:text-fuchsia-300">⌁</span>
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white">Nouvelle affectation</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">La validation de capacité est exécutée côté serveur.</p>
                </div>
            </div>

            <form wire:submit="save" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-600 dark:text-slate-300">Projet</label>
                    <select wire:model.live="projectId" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-fuchsia-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">
                        <option value="">Choisir un projet</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->titre }}</option>
                        @endforeach
                    </select>
                    @error('projectId') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-600 dark:text-slate-300">Collaborateur</label>
                    <select wire:model.live="userId" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-fuchsia-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">
                        <option value="">Choisir une personne</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                        @endforeach
                    </select>
                    @error('userId') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-600 dark:text-slate-300">Rôle mobilisé</label>
                    <input wire:model.live.debounce.300ms="roleRecherche" type="text" placeholder="Ex. Développeur Laravel" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-fuchsia-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">
                    @error('roleRecherche') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-2 flex items-center justify-between text-xs font-bold text-slate-600 dark:text-slate-300"><span>Taux de charge</span><span class="text-fuchsia-600 dark:text-fuchsia-300">{{ $percentageLoad }} %</span></label>
                    <input wire:model.live="percentageLoad" type="range" min="1" max="100" class="mt-3 w-full accent-fuchsia-500">
                    @error('percentageLoad') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-600 dark:text-slate-300">Début</label>
                    <input wire:model.live="startDate" type="date" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-fuchsia-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">
                    @error('startDate') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-600 dark:text-slate-300">Fin <span class="font-normal text-slate-400">(facultative)</span></label>
                    <input wire:model.live="endDate" type="date" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-fuchsia-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">
                    @error('endDate') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2 xl:col-span-3">
                    <button wire:loading.attr="disabled" class="rounded-2xl bg-fuchsia-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-fuchsia-500/20 transition hover:bg-fuchsia-500 disabled:opacity-50">Enregistrer l’affectation</button>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-slate-900/80">
            <div class="border-b border-slate-200 px-5 py-5 dark:border-white/10 sm:px-7"><h2 class="text-lg font-black text-slate-900 dark:text-white">Affectations actives et planifiées</h2></div>
            <div class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($assignments as $assignment)
                    <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-black text-slate-900 dark:text-white">{{ $assignment->user?->name }} <span class="font-normal text-slate-400">sur</span> {{ $assignment->project?->titre }}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $assignment->role_recherche ?: 'Contribution générale' }} · {{ $assignment->start_date?->format('d/m/Y') }} → {{ $assignment->end_date?->format('d/m/Y') ?: 'sans date de fin' }}</p>
                        </div>
                        <div class="flex items-center gap-3"><span class="rounded-full bg-fuchsia-100 px-3 py-1.5 text-xs font-black text-fuchsia-700 dark:bg-fuchsia-400/10 dark:text-fuchsia-200">{{ $assignment->percentage_load }} %</span><button wire:click="delete({{ $assignment->id }})" wire:confirm="Retirer cette affectation ?" class="rounded-xl border border-rose-200 px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:border-rose-300/20 dark:text-rose-300 dark:hover:bg-rose-400/10">Retirer</button></div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">Aucune affectation enregistrée pour le moment.</div>
                @endforelse
            </div>
            <div class="border-t border-slate-100 px-5 py-4 dark:border-white/5 sm:px-7">{{ $assignments->links() }}</div>
        </section>
    </div>
</div>
