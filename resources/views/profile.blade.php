<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight">
            {{ __('Mon profil') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 -mx-4 -mt-8 px-4 pb-28 pt-8 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8 lg:pb-8">
        <div class="mx-auto max-w-3xl space-y-6">
            <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.045] p-6 shadow-sm dark:shadow-2xl dark:shadow-black/20 backdrop-blur-xl dark:backdrop-blur-2xl sm:p-8">
                <livewire:profile.update-profile-information-form />
            </div>

            <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.045] p-6 shadow-sm dark:shadow-2xl dark:shadow-black/20 backdrop-blur-xl dark:backdrop-blur-2xl sm:p-8">
                <livewire:profile.update-password-form />
            </div>

            <div class="rounded-3xl border border-rose-200 dark:border-rose-400/15 bg-rose-50/60 dark:bg-rose-400/[0.03] p-6 shadow-sm dark:shadow-2xl dark:shadow-black/20 backdrop-blur-xl dark:backdrop-blur-2xl sm:p-8">
                <livewire:profile.delete-user-form />
            </div>

            <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.045] p-6 shadow-sm dark:shadow-2xl dark:shadow-black/20 backdrop-blur-xl dark:backdrop-blur-2xl sm:p-8">
                <h2 class="text-lg font-black text-slate-900 dark:text-white">{{ __('Déconnexion') }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Terminez votre session en toute sécurité.') }}</p>

                <form method="POST" action="{{ route('logout') }}" class="mt-5">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-2xl bg-rose-50 dark:bg-rose-500/15 px-5 py-3 text-xs font-black uppercase tracking-widest text-rose-600 dark:text-rose-300 transition hover:bg-rose-500 hover:text-white focus:outline-none focus:ring-4 focus:ring-rose-500/20">
                        {{ __('Se déconnecter') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
