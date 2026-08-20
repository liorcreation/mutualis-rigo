<x-ui.modal wireProperty="isOpen" onClose="close" title="Proposer un apport">
    <x-slot:header>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-300">Contribution rapide</p>
        <h2 class="mt-2 text-2xl font-black tracking-tight text-white">Faites avancer ce projet</h2>
        <p class="mt-2 text-sm text-slate-400">Votre apport sera vérifié avant d’être mutualisé.</p>
    </x-slot:header>

    <form wire:submit="save" class="space-y-5">
        <div class="grid grid-cols-2 gap-3">
            <button wire:click="$set('typeApport', 'financier')" type="button" class="rounded-2xl border px-4 py-4 text-left transition {{ $typeApport === 'financier' ? 'border-emerald-400/50 bg-emerald-400/10 text-emerald-200 shadow-lg shadow-emerald-950/30' : 'border-white/10 bg-white/[0.04] text-slate-400 hover:border-white/20 hover:text-white' }}">
                <svg class="mb-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m4-9.5c0-1.1-1.8-2-4-2s-4 .9-4 2 1.8 2 4 2 4 .9 4 2-1.8 2-4 2-4-.9-4-2" /></svg>
                <span class="block text-xs font-bold">Apport financier</span>
                <span class="mt-1 block text-[10px] opacity-60">Soutenir le budget</span>
            </button>
            <button wire:click="$set('typeApport', 'competence')" type="button" class="rounded-2xl border px-4 py-4 text-left transition {{ $typeApport === 'competence' ? 'border-fuchsia-400/50 bg-fuchsia-400/10 text-fuchsia-200 shadow-lg shadow-fuchsia-950/30' : 'border-white/10 bg-white/[0.04] text-slate-400 hover:border-white/20 hover:text-white' }}">
                <svg class="mb-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M9.5 13.5 7 16m4.5-7.5 2-2m-6 13 4-4m4-8 2-2m-4 12 4-4M4 20l3 1 13-13a2.1 2.1 0 0 0-3-3L4 18l-1 3Z" /></svg>
                <span class="block text-xs font-bold">Proposer une compétence</span>
                <span class="mt-1 block text-[10px] opacity-60">Partager votre expertise</span>
            </button>
        </div>
        @error('typeApport') <p class="text-xs font-medium text-rose-400">{{ $message }}</p> @enderror

        <div class="rounded-2xl border border-white/10 bg-black/10 px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Projet sélectionné</p>
            <p class="mt-1 text-sm font-bold text-slate-200">{{ $projectId ? 'Projet #'.$projectId : 'Sélectionnez un projet depuis le catalogue' }}</p>
            @error('projectId') <p class="mt-1 text-xs font-medium text-rose-400">{{ $message }}</p> @enderror
        </div>

        @if($typeApport === 'financier')
            <label class="block">
                <span class="mb-2 block text-xs font-bold text-slate-300">Montant proposé <span class="text-rose-400">*</span></span>
                <div class="relative">
                    <input wire:model.live.debounce.250ms="montant" type="number" min="1" step="0.01" placeholder="Ex. 150000" class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 pr-16 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-emerald-400/60 focus:ring-4 focus:ring-emerald-500/10 @error('montant') border-rose-400/70 @enderror">
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs font-bold text-slate-500">FCFA</span>
                </div>
                @error('montant') <span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span> @enderror
            </label>
        @else
            <label class="block">
                <span class="mb-2 block text-xs font-bold text-slate-300">Votre compétence <span class="text-rose-400">*</span></span>
                <textarea wire:model.live.debounce.250ms="descriptionApport" rows="3" placeholder="Ex. Développement Laravel, gestion de projet..." class="w-full resize-none rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-fuchsia-400/60 focus:ring-4 focus:ring-fuchsia-500/10 @error('descriptionApport') border-rose-400/70 @enderror"></textarea>
                @error('descriptionApport') <span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span> @enderror
            </label>
        @endif

        <label class="block">
            <span class="mb-2 block text-xs font-bold text-slate-300">Message <span class="font-normal text-slate-600">(optionnel)</span></span>
            <textarea wire:model.live.debounce.250ms="message" rows="2" placeholder="Ajoutez un mot au porteur du projet..." class="w-full resize-none rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10"></textarea>
        </label>

        <button type="submit" wire:loading.attr="disabled" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-5 py-4 text-sm font-black text-white shadow-xl shadow-indigo-950/40 transition hover:from-indigo-400 hover:to-fuchsia-400 disabled:cursor-wait disabled:opacity-60">
            <span wire:loading.remove>Envoyer ma contribution <span aria-hidden="true">→</span></span>
            <span wire:loading>Envoi en cours...</span>
        </button>
    </form>
</x-ui.modal>
