<div class="min-h-screen bg-slate-950 text-slate-100">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:py-12">
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 transition hover:text-white"><span>←</span> Retour à mon espace</a>

        <div class="mt-8 grid gap-8 lg:grid-cols-[0.8fr_1.4fr] lg:items-start">
            <div class="lg:sticky lg:top-28">
                <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-indigo-400/20 bg-indigo-400/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-indigo-300">
                    Nouvelle initiative
                </div>
                <h1 class="text-4xl font-black tracking-tight text-white sm:text-5xl">Donnez vie à votre <span class="bg-gradient-to-r from-indigo-300 to-fuchsia-300 bg-clip-text text-transparent">projet.</span></h1>
                <p class="mt-5 text-sm leading-7 text-slate-400">Décrivez votre besoin, réunissez les bonnes ressources et faites avancer votre initiative avec la communauté RIGO.</p>

                {{-- Indicateur des 3 étapes --}}
                <div class="mt-8 space-y-3">
                    <div class="flex items-center gap-3 rounded-2xl border p-4 transition {{ $step === 1 ? 'border-indigo-400/40 bg-indigo-400/10' : ($step > 1 ? 'border-emerald-400/20 bg-emerald-400/[0.04]' : 'border-white/10 bg-white/[0.04]') }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $step > 1 ? 'bg-emerald-400/10 text-emerald-300' : 'bg-indigo-400/10 text-indigo-300' }}">{{ $step > 1 ? '✓' : '01' }}</span>
                        <span class="text-xs font-bold {{ $step === 1 ? 'text-white' : 'text-slate-300' }}">Fiche projet</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl border p-4 transition {{ $step === 2 ? 'border-fuchsia-400/40 bg-fuchsia-400/10' : ($step > 2 ? 'border-emerald-400/20 bg-emerald-400/[0.04]' : 'border-white/10 bg-white/[0.04]') }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $step > 2 ? 'bg-emerald-400/10 text-emerald-300' : 'bg-fuchsia-400/10 text-fuchsia-300' }}">{{ $step > 2 ? '✓' : '02' }}</span>
                        <span class="text-xs font-bold {{ $step === 2 ? 'text-white' : 'text-slate-300' }}">Besoins RH</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl border p-4 transition {{ $step === 3 ? 'border-amber-400/40 bg-amber-400/10' : 'border-white/10 bg-white/[0.04]' }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-400/10 text-amber-300">03</span>
                        <span class="text-xs font-bold {{ $step === 3 ? 'text-white' : 'text-slate-300' }}">Besoins matériels</span>
                    </div>
                </div>
            </div>

            <form wire:submit="save" class="relative overflow-hidden rounded-3xl border border-white/10 bg-white/[0.055] p-5 shadow-2xl shadow-black/30 backdrop-blur-2xl sm:p-8">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-400 via-fuchsia-400 to-emerald-400"></div>
                <div class="mb-7 flex items-center justify-between border-b border-white/10 pb-5">
                    <div>
                        <h2 class="text-lg font-black text-white">Étape {{ $step }}/3 — {{ match($step) { 1 => 'Fiche projet', 2 => 'Besoins RH', default => 'Besoins matériels' } }}</h2>
                        <p class="mt-1 text-xs text-slate-500">Les champs marqués d’un * sont obligatoires.</p>
                    </div>
                    <span class="rounded-full border border-amber-300/20 bg-amber-400/10 px-3 py-1 text-[10px] font-bold text-amber-200">EN ÉTUDE</span>
                </div>

                {{-- Étape 1 : Fiche projet --}}
                @if($step === 1)
                    <div class="space-y-6">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="block sm:col-span-2"><span class="mb-2 block text-xs font-bold text-slate-300">Titre du projet <span class="text-rose-400">*</span></span><input wire:model.live.debounce.300ms="titre" type="text" placeholder="Ex. Ferme solaire communautaire" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('titre') border-rose-400/70 @enderror">@error('titre')<span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span>@enderror</label>
                            <label class="block sm:col-span-2"><span class="mb-2 block text-xs font-bold text-slate-300">Catégorie <span class="text-rose-400">*</span></span><input wire:model.live.debounce.300ms="categorie" type="text" placeholder="Ex. Énergie, agriculture, numérique..." class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('categorie') border-rose-400/70 @enderror">@error('categorie')<span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span>@enderror</label>
                            <label class="block sm:col-span-2"><span class="mb-2 block text-xs font-bold text-slate-300">Description détaillée <span class="text-rose-400">*</span></span><textarea wire:model.live.debounce.300ms="description" rows="5" placeholder="Quel problème votre projet résout-il ? Quels sont ses objectifs ?" class="w-full resize-none rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm leading-6 text-white outline-none transition placeholder:text-slate-600 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('description') border-rose-400/70 @enderror"></textarea>@error('description')<span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span>@enderror</label>
                        </div>

                        <div class="rounded-2xl border border-emerald-400/15 bg-emerald-400/[0.04] p-4 sm:p-5">
                            <div class="mb-4 flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-400/10 text-emerald-300">₣</span><div><h3 class="text-sm font-black text-emerald-100">Besoin financier</h3><p class="text-[11px] text-emerald-200/50">Définissez un objectif si votre projet nécessite un financement.</p></div></div>
                            <label class="block"><span class="mb-2 block text-xs font-bold text-slate-300">Montant cible <span class="font-normal text-slate-600">(optionnel)</span></span><div class="relative"><input wire:model.live.debounce.300ms="besoinFinancierTarget" type="number" min="1" step="0.01" placeholder="Ex. 2500000" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 pr-16 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-emerald-400/60 focus:ring-4 focus:ring-emerald-500/10 @error('besoinFinancierTarget') border-rose-400/70 @enderror"><span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs font-bold text-slate-500">FCFA</span></div>@error('besoinFinancierTarget')<span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span>@enderror</label>
                        </div>
                    </div>
                @endif

                {{-- Étape 2 : Besoins RH --}}
                @if($step === 2)
                    <div class="rounded-2xl border border-fuchsia-400/15 bg-fuchsia-400/[0.04] p-4 sm:p-5">
                        <div class="mb-4 flex items-center justify-between"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-fuchsia-400/10 text-fuchsia-300">✦</span><div><h3 class="text-sm font-black text-fuchsia-100">Compétences recherchées</h3><p class="text-[11px] text-fuchsia-200/50">Ajoutez les rôles et niveaux d'expérience nécessaires.</p></div></div><button wire:click="addCompetence" type="button" class="rounded-xl border border-fuchsia-300/20 px-3 py-2 text-[10px] font-bold text-fuchsia-200 transition hover:bg-fuchsia-400/10">+ Ajouter</button></div>
                        <div class="space-y-3">
                            @foreach($competences as $index => $competence)
                                <div wire:key="competence-{{ $index }}" class="grid grid-cols-[1fr_1fr_auto] gap-2">
                                    <div><input wire:model.live.debounce.300ms="competences.{{ $index }}.role" type="text" placeholder="Rôle / profil recherché" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-3 py-3 text-xs text-white outline-none transition placeholder:text-slate-600 focus:border-fuchsia-400/60 @error('competences.'.$index.'.role') border-rose-400/70 @enderror">@error('competences.'.$index.'.role')<span class="mt-1 block text-[10px] text-rose-400">{{ $message }}</span>@enderror</div>
                                    <div>
                                        <select wire:model.live="competences.{{ $index }}.niveau" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-3 py-3 text-xs text-white outline-none transition focus:border-fuchsia-400/60 @error('competences.'.$index.'.niveau') border-rose-400/70 @enderror">
                                            <option value="">Niveau d'expérience</option>
                                            <option value="débutant">Débutant</option>
                                            <option value="intermédiaire">Intermédiaire</option>
                                            <option value="senior">Senior</option>
                                            <option value="expert">Expert</option>
                                        </select>
                                    </div>
                                    <button wire:click="removeCompetence({{ $index }})" type="button" class="self-start rounded-xl border border-white/10 p-3 text-slate-500 transition hover:border-rose-400/30 hover:text-rose-300" aria-label="Supprimer la compétence">×</button>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-4 text-[11px] text-slate-500">Cette étape est optionnelle : passez à la suite si votre projet ne nécessite pas de compétence particulière.</p>
                    </div>
                @endif

                {{-- Étape 3 : Besoins matériels --}}
                @if($step === 3)
                    <div class="rounded-2xl border border-amber-400/15 bg-amber-400/[0.04] p-4 sm:p-5">
                        <div class="mb-4 flex items-center justify-between"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-400/10 text-amber-300">▦</span><div><h3 class="text-sm font-black text-amber-100">Matériel requis</h3><p class="text-[11px] text-amber-200/50">Listez les équipements, la quantité et la date à laquelle vous en aurez besoin.</p></div></div><button wire:click="addMateriel" type="button" class="rounded-xl border border-amber-300/20 px-3 py-2 text-[10px] font-bold text-amber-200 transition hover:bg-amber-400/10">+ Ajouter</button></div>
                        <div class="space-y-3">
                            @foreach($materiels as $index => $materiel)
                                <div wire:key="materiel-{{ $index }}" class="grid grid-cols-1 gap-2 sm:grid-cols-[2fr_0.8fr_1fr_auto]">
                                    <div>
                                        <input wire:model.live.debounce.300ms="materiels.{{ $index }}.nom" type="text" placeholder="Ex. Ordinateur portable" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-3 py-3 text-xs text-white outline-none transition placeholder:text-slate-600 focus:border-amber-400/60 @error('materiels.'.$index.'.nom') border-rose-400/70 @enderror">
                                        @error('materiels.'.$index.'.nom')<span class="mt-1 block text-[10px] text-rose-400">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <input wire:model.live.debounce.300ms="materiels.{{ $index }}.quantite" type="number" min="1" placeholder="Qté" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-3 py-3 text-xs text-white outline-none transition placeholder:text-slate-600 focus:border-amber-400/60 @error('materiels.'.$index.'.quantite') border-rose-400/70 @enderror">
                                        @error('materiels.'.$index.'.quantite')<span class="mt-1 block text-[10px] text-rose-400">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <input wire:model.live="materiels.{{ $index }}.date_souhaitee" type="date" class="w-full rounded-xl border border-white/10 bg-slate-950/70 px-3 py-3 text-xs text-white outline-none transition focus:border-amber-400/60 @error('materiels.'.$index.'.date_souhaitee') border-rose-400/70 @enderror">
                                        @error('materiels.'.$index.'.date_souhaitee')<span class="mt-1 block text-[10px] text-rose-400">{{ $message }}</span>@enderror
                                    </div>
                                    <button wire:click="removeMateriel({{ $index }})" type="button" class="self-start rounded-xl border border-white/10 px-3 py-3 text-slate-500 transition hover:border-rose-400/30 hover:text-rose-300" aria-label="Supprimer le matériel">×</button>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-4 text-[11px] text-slate-500">Cette étape est optionnelle : soumettez directement si votre projet ne nécessite pas de matériel.</p>
                    </div>
                @endif

                {{-- Navigation entre étapes --}}
                <div class="mt-8 flex items-center gap-3">
                    @if($step > 1)
                        <button wire:click="previousStep" type="button" class="flex items-center justify-center gap-2 rounded-2xl border border-white/10 px-5 py-4 text-sm font-black text-slate-300 transition hover:bg-white/10 hover:text-white">← Précédent</button>
                    @endif

                    @if($step < 3)
                        <button wire:click="nextStep" wire:loading.attr="disabled" type="button" class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-5 py-4 text-sm font-black text-white shadow-xl shadow-indigo-950/40 transition hover:from-indigo-400 hover:to-fuchsia-400 disabled:cursor-wait disabled:opacity-60">
                            <span wire:loading.remove wire:target="nextStep">Étape suivante <span aria-hidden="true">→</span></span>
                            <span wire:loading wire:target="nextStep">Vérification...</span>
                        </button>
                    @else
                        <button wire:loading.attr="disabled" wire:target="save" type="submit" class="flex flex-1 items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-5 py-4 text-sm font-black text-white shadow-xl shadow-indigo-950/40 transition hover:from-indigo-400 hover:to-fuchsia-400 disabled:cursor-wait disabled:opacity-60">
                            <span wire:loading.remove wire:target="save">Soumettre le projet <span aria-hidden="true">→</span></span>
                            <span wire:loading wire:target="save">Enregistrement...</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
