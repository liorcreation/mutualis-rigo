<div class="fixed right-4 top-[4.5rem] z-[60] lg:right-8 lg:top-6" wire:poll.15s>
    <div class="relative">
        <button wire:click="toggle" type="button" class="relative flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 text-slate-500 dark:text-slate-300 shadow-lg dark:shadow-xl shadow-black/5 dark:shadow-black/20 transition hover:border-indigo-300 dark:hover:border-indigo-400/40 hover:bg-indigo-50 dark:hover:bg-indigo-500/15 hover:text-indigo-600 dark:hover:text-white" aria-label="Notifications" aria-expanded="{{ $open ? 'true' : 'false' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17H9m9-3V9a6 6 0 0 0-12 0v5l-2 2h16l-2-2Zm-4 7a2.5 2.5 0 0 1-4 0" /></svg>
            @if($unreadCount > 0)
                <span class="absolute -right-1 -top-1 flex min-h-5 min-w-5 items-center justify-center rounded-full border-2 border-white dark:border-slate-950 bg-rose-500 px-1 text-[9px] font-black text-white">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
            @endif
        </button>

        @if($open)
            <div class="absolute right-0 mt-3 w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 shadow-2xl shadow-black/10 dark:shadow-black/40">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-white/10 px-5 py-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-300">Centre d'alertes</p>
                        <h2 class="mt-1 text-sm font-black text-slate-900 dark:text-white">Notifications</h2>
                    </div>
                    @if($unreadCount > 0)
                        <button wire:click="markAllAsRead" type="button" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-300 transition hover:text-indigo-800 dark:hover:text-white">Tout lire</button>
                    @endif
                </div>

                <div class="flex items-center gap-2 border-b border-slate-200 dark:border-white/10 px-5 py-3">
                    <button wire:click="setFilter('all')" type="button" class="rounded-xl px-3 py-1.5 text-[10px] font-bold transition {{ $filter === 'all' ? 'bg-indigo-500 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white' }}">Toutes</button>
                    <button wire:click="setFilter('unread')" type="button" class="rounded-xl px-3 py-1.5 text-[10px] font-bold transition {{ $filter === 'unread' ? 'bg-indigo-500 text-white' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white' }}">Non lues</button>
                </div>

                <div class="max-h-[24rem] divide-y divide-slate-200 dark:divide-white/10 overflow-y-auto">
                    @forelse($notifications as $notification)
                        @php($data = $notification->data)
                        <button wire:click="open('{{ $notification->id }}')" type="button" class="block w-full px-5 py-4 text-left transition hover:bg-slate-50 dark:hover:bg-white/[0.06] {{ $notification->read_at ? 'opacity-60' : 'bg-indigo-50/70 dark:bg-indigo-500/[0.06]' }}">
                            <div class="flex items-start gap-3">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-slate-300 dark:bg-slate-700' : 'bg-indigo-400' }}"></span>
                                <div class="min-w-0">
                                    <p class="text-xs font-black text-slate-900 dark:text-white">{{ $data['title'] ?? 'Notification RIGO' }}</p>
                                    <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $data['message'] ?? '' }}</p>
                                    <p class="mt-2 text-[10px] font-medium text-slate-400 dark:text-slate-600">{{ $notification->created_at?->diffForHumans() }}</p>
                                </div>
                            </div>
                        </button>
                    @empty
                        <x-ui.empty-state :bordered="false" heading="Aucune notification" text="Vous êtes à jour.">
                            <x-slot:icon>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17H9m9-3V9a6 6 0 0 0-12 0v5l-2 2h16l-2-2" /></svg>
                            </x-slot:icon>
                        </x-ui.empty-state>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
