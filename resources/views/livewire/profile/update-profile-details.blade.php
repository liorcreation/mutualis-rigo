<section>
    <header>
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 dark:text-white">Fiche métier</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ces informations permettent à RIGO de comprendre les ressources que vous pouvez mutualiser.</p>
            </div>
            <span class="shrink-0 rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-wider {{ $isVerified ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-200' : 'bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-200' }}">{{ $isVerified ? 'Profil vérifié' : 'À compléter' }}</span>
        </div>
    </header>

    <form wire:submit="save" class="mt-6 space-y-5">
        @if ($isCompany)
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block sm:col-span-2"><span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">Nom de l’entreprise</span><input wire:model.live="nomEntreprise" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">@error('nomEntreprise')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                <label class="block"><span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">RNE / SIRET</span><input wire:model.live="rneSiret" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"></label>
                <label class="block"><span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">Secteur d’activité</span><input wire:model.live="secteurActivite" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"></label>
                <label class="block"><span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">Représentant légal</span><input wire:model.live="representantLegal" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"></label>
                <label class="block"><span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">Site web</span><input wire:model.live="siteWeb" type="url" placeholder="https://" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">@error('siteWeb')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block"><span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">Prénom</span><input wire:model.live="prenom" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">@error('prenom')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                <label class="block"><span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">Nom</span><input wire:model.live="nom" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white">@error('nom')<span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>@enderror</label>
                <label class="block sm:col-span-2"><span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">Titre professionnel</span><input wire:model.live="titreProfessionnel" type="text" placeholder="Ex. Ingénieur logiciel" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"></label>
            </div>
        @endif

        <label class="block"><span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">Compétences</span><input wire:model.live="competencesText" type="text" placeholder="Laravel, gestion de projet, comptabilité" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"><span class="mt-1 block text-[11px] text-slate-400">Séparez les compétences par des virgules.</span></label>
        <label class="block"><span class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">Biographie / présentation</span><textarea wire:model.live="biographie" rows="4" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm outline-none focus:border-indigo-400 dark:border-white/10 dark:bg-slate-950/70 dark:text-white"></textarea></label>
        <button type="submit" class="rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-6 py-3 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-950/20 transition hover:from-indigo-400 hover:to-fuchsia-400">Enregistrer ma fiche</button>
        <span x-data="{ shown: false, timeout: null }" x-init="@this.on('profile-details-updated', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => shown = false, 2500); })" x-show="shown" x-cloak class="ml-3 text-xs font-bold text-emerald-600 dark:text-emerald-300">Fiche enregistrée.</span>
    </form>
</section>
