<x-guest-layout>
    <div class="relative overflow-hidden rounded-3xl border border-emerald-400/20 bg-slate-900/80 p-8 text-center shadow-2xl backdrop-blur-2xl">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-400">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
        </div>
        <p class="mt-5 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-400">Document authentique</p>
        <h1 class="mt-2 text-2xl font-black tracking-tight text-white">{{ $contract->contract_number }}</h1>
        <p class="mt-3 text-sm leading-6 text-slate-400">
            Contrat de mutualisation signé pour le projet
            <span class="font-bold text-slate-200">{{ $contract->project?->titre ?? 'Projet supprimé' }}</span>.
        </p>

        <div class="mt-6 grid grid-cols-2 gap-3 text-left">
            <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3">
                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500">Signé le</p>
                <p class="mt-1 text-sm font-bold text-slate-100">{{ $contract->signed_at?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3">
                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500">Statut</p>
                <p class="mt-1 text-sm font-bold text-slate-100">{{ $contract->status->label() }}</p>
            </div>
        </div>

        <p class="mt-6 break-all rounded-xl bg-slate-950/60 px-3 py-2 font-mono text-[10px] text-slate-500">
            {{ $contract->document_hash }}
        </p>
    </div>
</x-guest-layout>
