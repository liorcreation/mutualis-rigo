<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:py-12">
        <a href="{{ route('projects.index') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 transition hover:text-slate-900 dark:hover:text-white">
            ← Retour au catalogue
        </a>

        <x-ui.card glow class="mt-6">
            <span class="rounded-full border border-indigo-200 dark:border-indigo-300/20 bg-indigo-50 dark:bg-indigo-400/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-300">
                {{ $project->categorie ?: 'Projet RIGO' }}
            </span>
            <h1 class="mt-5 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">{{ $project->titre }}</h1>
            <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-500 dark:text-slate-400">{{ $project->description }}</p>

            <div class="mt-6 flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                <span class="rounded-xl bg-slate-100 dark:bg-white/[0.06] px-3 py-2">Porteur : <strong class="text-slate-800 dark:text-slate-200">{{ $project->user?->name }}</strong></span>
                <span class="rounded-xl bg-slate-100 dark:bg-white/[0.06] px-3 py-2">Statut : <strong class="text-indigo-600 dark:text-indigo-300">{{ $project->statut->label() }}</strong></span>
                @if($project->besoin_financier_target)
                    <span class="rounded-xl bg-emerald-50 dark:bg-emerald-400/10 px-3 py-2 text-emerald-700 dark:text-emerald-300">Objectif : {{ number_format((float) $project->besoin_financier_target, 0, ',', ' ') }} FCFA</span>
                @endif
            </div>
        </x-ui.card>

        @if(session('faq-message'))
            <div class="mt-6 rounded-2xl border border-emerald-200 dark:border-emerald-400/20 bg-emerald-50 dark:bg-emerald-400/10 px-4 py-3 text-sm font-semibold text-emerald-700 dark:text-emerald-200">{{ session('faq-message') }}</div>
        @endif

        <section class="mt-8 rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.045] p-5 shadow-sm dark:shadow-xl sm:p-7" wire:poll.5s>
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-white/10 pb-5">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-fuchsia-600 dark:text-fuchsia-300">Espace public</p>
                    <h2 class="mt-2 text-xl font-black text-slate-900 dark:text-white">Questions & réponses</h2>
                </div>
                <span class="text-xs font-bold text-slate-500">{{ $questions->count() }} question(s)</span>
            </div>

            @auth
                <form wire:submit="ask" class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <textarea wire:model.live.debounce.300ms="question" rows="2" placeholder="Posez une question au porteur du projet..." class="min-h-12 flex-1 resize-none rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950/70 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:border-fuchsia-400/60 focus:ring-4 focus:ring-fuchsia-500/10 @error('question') border-rose-400/70 @enderror"></textarea>
                    <button wire:loading.attr="disabled" type="submit" class="rounded-2xl bg-fuchsia-500 px-5 py-3 text-xs font-black text-white transition hover:bg-fuchsia-400 disabled:opacity-50">Publier</button>
                </form>
                @error('question')
                    <p class="mt-2 text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p>
                @enderror
            @else
                <div class="mt-6 rounded-2xl border border-indigo-200 dark:border-indigo-400/15 bg-indigo-50 dark:bg-indigo-400/[0.06] px-4 py-3 text-xs text-slate-600 dark:text-slate-400">
                    Connectez-vous pour poser une question. <a href="{{ route('login') }}" wire:navigate class="font-bold text-indigo-600 dark:text-indigo-300 hover:text-slate-900 dark:hover:text-white">Se connecter →</a>
                </div>
            @endauth

            <div class="mt-7 divide-y divide-slate-200 dark:divide-white/10">
                @forelse($questions as $item)
                    <article wire:key="question-{{ $item->id }}" class="py-6 first:pt-0">
                        <div class="flex gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-400/10 text-xs font-black text-indigo-600 dark:text-indigo-300">
                                {{ strtoupper(substr($item->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-black text-slate-900 dark:text-white">{{ $item->user?->name ?? 'Utilisateur' }}</span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-600">{{ $item->created_at?->diffForHumans() }}</span>
                                </div>
                                <p class="mt-2 text-sm leading-6 text-slate-700 dark:text-slate-300">{{ $item->question }}</p>
                            </div>
                        </div>

                        @if($item->answer)
                            <div class="ml-12 mt-4 rounded-2xl border border-emerald-200 dark:border-emerald-400/15 bg-emerald-50 dark:bg-emerald-400/[0.05] px-4 py-3">
                                <p class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300">
                                    Réponse du porteur
                                    @if($item->answered_at)
                                        <span class="ml-2 font-normal text-emerald-600/60 dark:text-emerald-300/50">· {{ $item->answered_at->diffForHumans() }}</span>
                                    @endif
                                </p>
                                <p class="mt-2 text-sm leading-6 text-slate-700 dark:text-slate-300">{{ $item->answer }}</p>
                            </div>
                        @elseif(auth()->check() && auth()->id() === $project->user_id)
                            <div class="ml-12 mt-3">
                                @if($answeringQuestionId === $item->id)
                                    <form wire:submit="saveAnswer" class="space-y-2">
                                        <textarea wire:model.live.debounce.300ms="answer" rows="3" placeholder="Votre réponse..." class="w-full resize-none rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950/70 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none focus:border-emerald-400/60"></textarea>
                                        @error('answer')
                                            <p class="text-xs text-rose-500 dark:text-rose-400">{{ $message }}</p>
                                        @enderror
                                        <div class="flex gap-2">
                                            <button type="submit" class="rounded-xl bg-emerald-500 px-3 py-2 text-[10px] font-black text-white">Répondre</button>
                                            <button wire:click="cancelAnswer" type="button" class="rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2 text-[10px] font-bold text-slate-500 dark:text-slate-400">Annuler</button>
                                        </div>
                                    </form>
                                @else
                                    <button wire:click="startAnswer({{ $item->id }})" type="button" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-300 hover:text-slate-900 dark:hover:text-white">Répondre à cette question →</button>
                                @endif
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="py-12 text-center">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Aucune question pour le moment</p>
                        <p class="mt-2 text-xs text-slate-500">Soyez le premier à demander une précision.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
