<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 dark:text-white tracking-tight transition-colors">
                    {{ __('Mon Espace') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Ce que vous faites et les choses qu'on partage ensemble</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-sm self-start md:self-auto transition-colors">
                <span>Connecté :</span>
                <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <!-- APPEL DU COMPOSANT LIVEWIRE EN TEMPS RÉEL -->
        <livewire:dashboard-stats />
    </div>
</x-app-layout>