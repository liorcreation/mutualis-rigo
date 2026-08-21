<div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100">
    <div class="mx-auto flex min-h-[calc(100vh-8rem)] max-w-5xl flex-col px-4 py-6 sm:px-6 lg:py-10">
        <div class="mb-5 flex items-center gap-3">
            <a href="{{ route('projects.show', $project) }}" wire:navigate class="rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2 text-xs font-bold text-slate-500 dark:text-slate-400 transition hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white">← Projet</a>
            <div class="min-w-0">
                <p class="truncate text-sm font-black text-slate-900 dark:text-white">{{ $project->titre }}</p>
                <p class="text-[10px] text-slate-500">Conversation privée avec {{ $participant->name }}</p>
            </div>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.045] shadow-sm dark:shadow-2xl dark:shadow-black/20 backdrop-blur-xl dark:backdrop-blur-2xl">
            <div class="flex items-center gap-3 border-b border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-white/[0.04] px-5 py-4 sm:px-7">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-400/15 text-indigo-600 dark:text-indigo-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19a4 4 0 0 0-8 0m4-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm8 8a3 3 0 0 0-5.5-1.7M17 11a2.5 2.5 0 1 0-1.5-4.5" /></svg>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-900 dark:text-white">{{ $participant->name }}</p>
                    <p class="mt-1 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-300"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span> Espace sécurisé</p>
                </div>
            </div>

            <div id="project-chat-messages" wire:poll.5s="refreshMessages" class="flex-1 space-y-4 overflow-y-auto px-4 py-6 sm:px-7" style="min-height: 22rem; max-height: calc(100vh - 24rem);">
                @forelse($messages as $message)
                    @php($mine = $message->sender_id === auth()->id())
                    <div wire:key="message-{{ $message->id }}" class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] sm:max-w-[70%]">
                            <div class="rounded-2xl px-4 py-3 text-sm leading-6 {{ $mine ? 'rounded-br-md bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-950/30' : 'rounded-bl-md border border-slate-200 dark:border-white/10 bg-slate-100 dark:bg-slate-800/80 text-slate-800 dark:text-slate-200' }}">
                                @if($message->content !== '')
                                    <p class="whitespace-pre-wrap">{{ $message->content }}</p>
                                @endif
                                @if($message->attachment_path)
                                    <a href="{{ Storage::disk('public')->url($message->attachment_path) }}" target="_blank" class="mt-2 flex items-center gap-2 rounded-xl border border-white/15 bg-black/10 px-3 py-2 text-xs font-bold underline-offset-2 hover:underline">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7l-4-4Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4h4" /></svg>
                                        Ouvrir la pièce jointe
                                    </a>
                                @endif
                            </div>
                            <p class="mt-1 px-1 text-[10px] text-slate-400 dark:text-slate-600 {{ $mine ? 'text-right' : '' }}">
                                {{ $message->created_at?->format('d/m/Y · H:i') }}
                                @if($mine && $message->read_at)
                                    <span class="text-indigo-600 dark:text-indigo-400">· Lu</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="flex h-full min-h-[20rem] items-center justify-center">
                        <x-ui.empty-state heading="Démarrez la conversation" text="Échangez sur les détails de la collaboration.">
                            <x-slot:icon>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8m-8 4h5m8-2a9 9 0 1 1-3-6.7L21 5l-.7 5.2A9 9 0 0 1 21 12Z" /></svg>
                            </x-slot:icon>
                        </x-ui.empty-state>
                    </div>
                @endforelse
            </div>

            <form wire:submit="send" class="border-t border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-black/10 p-4 sm:p-5">
                <div class="flex items-end gap-3">
                    <label class="flex-1">
                        <span class="sr-only">Votre message</span>
                        <textarea wire:model.live="content" rows="2" placeholder="Écrire un message..." class="w-full resize-none rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950/70 px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10 @error('content') border-rose-400/70 @enderror"></textarea>
                    </label>
                    <label class="flex h-11 w-11 shrink-0 cursor-pointer items-center justify-center rounded-xl border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 transition hover:border-indigo-300 dark:hover:border-indigo-400/40 hover:bg-indigo-50 dark:hover:bg-indigo-400/10 hover:text-indigo-600 dark:hover:text-indigo-300" title="Joindre un document">
                        <input wire:model="attachment" type="file" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.jpg,.jpeg,.png">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" d="m21.4 11.6-8.8 8.8a6 6 0 0 1-8.5-8.5l9.2-9.2a4 4 0 0 1 5.7 5.7l-9.2 9.2a2 2 0 0 1-2.8-2.8l8.5-8.5" /></svg>
                    </label>
                    <button wire:loading.attr="disabled" type="submit" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-500 text-white shadow-lg shadow-indigo-950/30 transition hover:bg-indigo-400 disabled:opacity-50" aria-label="Envoyer">
                        <svg wire:loading.remove class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 14-7-4 14-3-6-7-1Z" /></svg>
                        <span wire:loading class="text-xs">...</span>
                    </button>
                </div>
                @if($attachment)
                    <p class="mt-2 text-xs text-indigo-600 dark:text-indigo-300">Fichier prêt : {{ $attachment->getClientOriginalName() }}</p>
                @endif
                @error('attachment')
                    <p class="mt-2 text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p>
                @enderror
                @error('content')
                    <p class="mt-2 text-xs font-medium text-rose-500 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </form>
        </div>
    </div>
    @script
        <script>
            $wire.on('message-sent', () => {
                requestAnimationFrame(() => {
                    const container = document.getElementById('project-chat-messages');
                    if (container) container.scrollTop = container.scrollHeight;
                });
            });
        </script>
    @endscript
</div>
