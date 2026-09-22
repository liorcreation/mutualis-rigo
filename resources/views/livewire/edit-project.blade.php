<div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:py-12">
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-wider text-slate-500 transition hover:text-indigo-600 dark:hover:text-indigo-300">← Retour à mon espace</a>

        <div class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white/80 shadow-xl shadow-slate-900/5 backdrop-blur-xl dark:border-white/10 dark:bg-white/[0.045] dark:shadow-black/20">
            <div class="relative overflow-hidden border-b border-slate-200/80 p-6 dark:border-white/10 sm:p-8">
                <div class="pointer-events-none absolute -right-20 -top-28 h-64 w-64 rounded-full bg-indigo-400/15 blur-3xl"></div>
                <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-indigo-600 dark:text-indigo-300">Édition sécurisée</p>
                        <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">Affiner votre projet</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">Mettez à jour les besoins de « {{ $project->titre }} ». Le projet reste soumis aux règles de validation RIGO.</p>
                    </div>
                    <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-indigo-700 dark:border-indigo-400/20 dark:bg-indigo-400/10 dark:text-indigo-200">{{ $project->statut->label() }}</span>
                </div>
            </div>

            <div class="grid grid-cols-3 border-b border-slate-200/80 dark:border-white/10">
                @foreach(['Fiche projet', 'Compétences', 'Matériel'] as $index => $label)
                    <button wire:click="$set('step', {{ $index + 1 }})" type="button" class="relative px-3 py-4 text-[10px] font-black uppercase tracking-wider transition sm:text-xs {{ $step === $index + 1 ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                        <span class="mr-1 inline-flex h-6 w-6 items-center justify-center rounded-full {{ $step === $index + 1 ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-slate-400' }}">{{ $index + 1 }}</span>
                        {{ $label }}
                        @if($step === $index + 1)<span class="absolute inset-x-0 bottom-0 h-0.5 bg-indigo-500"></span>@endif
                    </button>
                @endforeach
            </div>

            <form wire:submit="save" class="p-6 sm:p-8">
                @if($step === 1)
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block sm:col-span-2"><span class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Titre du projet</span><input wire:model.live="titre" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-950/70 dark:text-white @error('titre') border-rose-400 @enderror">@error('titre')<span class="mt-2 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                        <label class="block"><span class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Catégorie</span><input wire:model.live="categorie" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-950/70 dark:text-white @error('categorie') border-rose-400 @enderror">@error('categorie')<span class="mt-2 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                        <label class="block"><span class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Budget cible (FCFA)</span><input wire:model.live="besoinFinancierTarget" type="number" min="1" step="0.01" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-950/70 dark:text-white @error('besoinFinancierTarget') border-rose-400 @enderror">@error('besoinFinancierTarget')<span class="mt-2 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                        <label class="block sm:col-span-2"><span class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Description</span><textarea wire:model.live="description" rows="7" class="w-full resize-none rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm leading-6 outline-none transition focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 dark:border-white/10 dark:bg-slate-950/70 dark:text-white @error('description') border-rose-400 @enderror"></textarea>@error('description')<span class="mt-2 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                    </div>
                @elseif($step === 2)
                    <div class="flex items-start justify-between gap-4"><div><p class="text-lg font-black text-slate-900 dark:text-white">Compétences recherchées</p><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Décrivez le rôle et le niveau attendu pour améliorer le rapprochement.</p></div><button wire:click="addCompetence" type="button" class="rounded-xl bg-fuchsia-50 px-3 py-2 text-[10px] font-black text-fuchsia-700 dark:bg-fuchsia-400/10 dark:text-fuchsia-200">+ Ajouter</button></div>
                    <div class="mt-6 space-y-4">@foreach($competences as $index => $competence)<div wire:key="edit-competence-{{ $index }}" class="grid gap-3 rounded-2xl border border-slate-200 p-4 dark:border-white/10 sm:grid-cols-[1fr_180px_auto]"><input wire:model.live="competences.{{ $index }}.role" type="text" placeholder="Rôle recherché" class="rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm outline-none focus:border-fuchsia-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"><input wire:model.live="competences.{{ $index }}.niveau" type="text" placeholder="Niveau" class="rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm outline-none focus:border-fuchsia-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"><button wire:click="removeCompetence({{ $index }})" type="button" class="rounded-xl px-3 py-2 text-xs font-black text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-400/10">Supprimer</button></div>@endforeach</div>
                @else
                    <div class="flex items-start justify-between gap-4"><div><p class="text-lg font-black text-slate-900 dark:text-white">Matériel requis</p><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Précisez la quantité nécessaire et la date souhaitée.</p></div><button wire:click="addMateriel" type="button" class="rounded-xl bg-amber-50 px-3 py-2 text-[10px] font-black text-amber-700 dark:bg-amber-400/10 dark:text-amber-200">+ Ajouter</button></div>
                    <div class="mt-6 space-y-4">@foreach($materiels as $index => $materiel)<div wire:key="edit-materiel-{{ $index }}" class="grid gap-3 rounded-2xl border border-slate-200 p-4 dark:border-white/10 sm:grid-cols-[1fr_120px_170px_auto]"><input wire:model.live="materiels.{{ $index }}.nom" type="text" placeholder="Équipement requis" class="rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm outline-none focus:border-amber-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"><input wire:model.live="materiels.{{ $index }}.quantite" type="number" min="1" placeholder="Qté" class="rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm outline-none focus:border-amber-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"><input wire:model.live="materiels.{{ $index }}.date_souhaitee" type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm outline-none focus:border-amber-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"><button wire:click="removeMateriel({{ $index }})" type="button" class="rounded-xl px-3 py-2 text-xs font-black text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-400/10">Supprimer</button></div>@endforeach</div>
                @endif

                <div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('dashboard') }}" wire:navigate class="rounded-2xl px-4 py-3 text-center text-xs font-black text-slate-500 transition hover:bg-slate-100 dark:hover:bg-white/10">Annuler</a>
                    <div class="flex gap-3">@if($step > 1)<button wire:click="previousStep" type="button" class="rounded-2xl border border-slate-200 px-5 py-3 text-xs font-black text-slate-600 dark:border-white/10 dark:text-slate-300">Précédent</button>@endif @if($step < 3)<button wire:click="nextStep" type="button" class="rounded-2xl bg-indigo-500 px-5 py-3 text-xs font-black text-white shadow-lg shadow-indigo-950/20">Continuer</button>@else<button wire:loading.attr="disabled" type="submit" class="rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-5 py-3 text-xs font-black text-white shadow-lg shadow-indigo-950/20 disabled:opacity-50"><span wire:loading.remove>Enregistrer les modifications</span><span wire:loading>Enregistrement...</span></button>@endif</div>
                </div>
            </form>
        </div>
    </div>
</div>
