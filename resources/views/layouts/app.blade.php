<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MUTUALIS') }} — Espace de Partage</title>

    <!-- Correctif Anti-Flicker / Alpine Cloak -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <!-- Polices -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Script Anti-Flicker / Thème Sombre -->
    <script>
        function applyTheme() {
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    </script>
</head>
<body class="font-['Plus_Jakarta_Sans'] antialiased h-full text-slate-900 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 transition-colors duration-300 selection:bg-indigo-500 selection:text-white">

    <div x-data="{ sidebarOpen: true }" class="min-h-screen flex flex-col bg-slate-50 dark:bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] dark:from-slate-900 dark:via-slate-950 dark:to-black">

        <!-- Sidebar desktop rétractable -->
        <aside class="fixed inset-y-0 left-0 z-50 hidden flex-col border-r border-slate-200/80 dark:border-slate-800/80 bg-white/90 dark:bg-slate-950/90 px-3 py-5 shadow-2xl shadow-slate-950/5 dark:shadow-black/20 backdrop-blur-2xl transition-all duration-300 lg:flex" :class="sidebarOpen ? 'w-72' : 'w-20'">
            <div class="flex items-center justify-between px-3">
                <a href="{{ auth()->check() ? route('projects.index') : route('home') }}" wire:navigate class="flex items-center gap-3 overflow-hidden">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-emerald-400 text-white shadow-lg shadow-indigo-500/20">✦</div>
                    <div x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                        <span class="block text-lg font-black tracking-tight text-slate-900 dark:text-white">RIGO</span>
                        <span class="block text-[9px] font-bold uppercase tracking-[0.2em] text-indigo-500 dark:text-indigo-400">Mutualisation</span>
                    </div>
                </a>
                <div class="flex items-center gap-1">
                    <button x-data="{
                            isDark: document.documentElement.classList.contains('dark'),
                            init() { this.isDark = document.documentElement.classList.contains('dark'); },
                            toggle() {
                                this.isDark = !this.isDark;
                                document.documentElement.classList.toggle('dark', this.isDark);
                                localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                            }
                        }" @click="toggle()" type="button" class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-indigo-400" :aria-label="isDark ? 'Passer en thème clair' : 'Passer en thème sombre'" title="Basculer de thème">
                        <svg x-show="!isDark" x-cloak class="h-4 w-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                        <svg x-show="isDark" x-cloak class="h-4 w-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    </button>
                    <button @click="sidebarOpen = !sidebarOpen" type="button" class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-indigo-400" aria-label="Rétracter la sidebar">
                        <svg class="h-4 w-4 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
                    </button>
                </div>
            </div>

            <nav class="mt-10 flex-1 space-y-2">
                @guest
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 transition hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z" /><path stroke-linecap="round" d="M9 21v-7h6v7" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Accueil</span>
                    </a>
                @endguest
                <a href="{{ route('projects.index') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 transition hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-300">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5v-11Z" /><path stroke-linecap="round" d="M8 8h8m-8 4h8m-8 4h5" /></svg>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Catalogue projets</span>
                </a>
                @auth
                    <a href="{{ route('dashboard', ['activeTab' => 'contributions']) }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 transition hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-4-4 4 4 4-4m-8-8 4 4 4-4" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Mes contributions</span>
                    </a>
                    <a href="{{ route('payments.history') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 transition hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h5M4 6h16a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Mes paiements</span>
                    </a>
                    <a href="{{ route('material.reservations') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 transition hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Réservation matériel</span>
                    </a>
                    <a href="{{ url('/profile') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 transition hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19a4 4 0 0 0-8 0m4-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm8 8a3 3 0 0 0-5.5-1.7M17 11a2.5 2.5 0 1 0-1.5-4.5" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Mon profil</span>
                    </a>
                    @if(auth()->user()->canAccessBackOffice())
                        <div x-show="sidebarOpen" x-transition class="px-3 pb-1 pt-7 text-[9px] font-black uppercase tracking-[0.2em] text-slate-500">Back-office</div>
                        <a href="{{ route('admin.contributions') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 transition hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-600 dark:hover:text-emerald-300">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 3v18m-4-4 4 4 4-4m-8-8 4 4 4-4" /></svg>
                            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Valider les apports</span>
                        </a>
                        @if(auth()->user()->canReviewProjects())
                            <a href="{{ route('admin.projects.review') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 transition hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-300">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5h14v14H5zM8 9h8m-8 3h5" /></svg>
                                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Revoir les projets</span>
                            </a>
                        @endif
                        <a href="{{ Route::has('admin.audit') ? route('admin.audit') : url('/admin/audit') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-600 dark:text-slate-400 transition hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 dark:hover:text-indigo-300">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 0 12 2.944a11.955 11.955 0 0 0-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016Z" /></svg>
                            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Historique sécurisé</span>
                        </a>
                    @endif
                @endauth
            </nav>

            @auth
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-rose-600 dark:text-rose-400 transition hover:bg-rose-50 dark:hover:bg-rose-950/40">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4m-4-4 4-4-4-4m4 4H3" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Déconnexion</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" wire:navigate class="mt-2 flex items-center gap-3 rounded-2xl bg-indigo-600 px-3 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-500">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4m0-4 4-4-4-4m4 4H9" /></svg>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Connexion</span>
                </a>
            @endauth

            <div x-show="sidebarOpen" x-transition class="mt-2 rounded-2xl border border-indigo-200 dark:border-indigo-400/20 bg-indigo-50 dark:bg-indigo-500/10 p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-300">RIGO Connect</p>
                <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-slate-400">Mutualisez vos ressources, accélérez les projets.</p>
            </div>
        </aside>

        <!-- Barre de Navigation Principale (mobile/tablette) -->
        <nav x-data="{ menuOpen: false }" @keydown.escape.window="menuOpen = false" class="sticky top-0 z-50 backdrop-blur-xl bg-white/80 dark:bg-slate-950/75 border-b border-slate-200/80 dark:border-slate-800/80 transition-all duration-300 shadow-sm lg:hidden">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <div class="flex h-16 items-center justify-between sm:h-20">

                    <!-- Logo MUTUALIS -->
                    <a href="{{ auth()->check() ? route('projects.index') : (Route::has('home') ? route('home') : '/') }}" class="flex min-w-0 items-center gap-2.5 cursor-pointer select-none group sm:gap-3" wire:navigate>
                        <div class="h-9 w-9 shrink-0 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-emerald-400 p-0.5 shadow-lg shadow-indigo-500/20 transition-all duration-300 sm:h-10 sm:w-10">
                            <div class="flex h-full w-full items-center justify-center rounded-[10px] bg-slate-950">
                                <svg class="h-4 w-4 text-indigo-400 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="min-w-0 truncate">
                            <span class="text-lg font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-xl">MUTUALIS</span>
                            <span class="hidden text-[10px] font-mono tracking-widest text-indigo-500 dark:text-indigo-400 uppercase font-semibold sm:block">Espace de Partage</span>
                        </div>
                    </a>

                    <div class="flex shrink-0 items-center gap-1.5">
                        <!-- Bouton Toggle Thème -->
                        <div wire:ignore id="theme-toggle-container">
                            <button x-data="{
                                isDark: document.documentElement.classList.contains('dark'),
                                init() {
                                    this.isDark = document.documentElement.classList.contains('dark');
                                },
                                toggle() {
                                    this.isDark = !this.isDark;
                                    if (this.isDark) {
                                        document.documentElement.classList.add('dark');
                                        localStorage.setItem('theme', 'dark');
                                    } else {
                                        document.documentElement.classList.remove('dark');
                                        localStorage.setItem('theme', 'light');
                                    }
                                }
                            }" @click="toggle()" type="button" class="rounded-xl bg-slate-200/80 dark:bg-slate-900/80 p-2.5 text-slate-700 dark:text-slate-300 border border-slate-300/80 dark:border-slate-800 transition hover:bg-slate-300 dark:hover:bg-slate-800" :aria-label="isDark ? 'Passer en thème clair' : 'Passer en thème sombre'" title="Basculer de thème">
                                <svg x-show="!isDark" x-cloak class="h-4 w-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                                <svg x-show="isDark" x-cloak class="h-4 w-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>

                        <!-- Bouton menu (secondaire) -->
                        <button @click="menuOpen = !menuOpen" type="button" class="rounded-xl p-2.5 text-slate-600 dark:text-slate-300 border border-slate-300/80 dark:border-slate-800 bg-slate-200/80 dark:bg-slate-900/80 transition hover:bg-slate-300 dark:hover:bg-slate-800" :aria-expanded="menuOpen ? 'true' : 'false'" aria-label="Ouvrir le menu">
                            <svg x-show="!menuOpen" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            <svg x-show="menuOpen" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" /></svg>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Panneau "plus d'options" -->
            <div x-show="menuOpen" x-cloak x-transition.duration.150ms @click.outside="menuOpen = false" class="border-t border-slate-200/80 dark:border-slate-800/80 bg-white/95 dark:bg-slate-950/95 px-4 py-3 sm:px-6">
                <div class="mx-auto flex max-w-7xl flex-col gap-1">
                    @guest
                        <a href="{{ Route::has('home') ? route('home') : '/' }}" wire:navigate @click="menuOpen = false" class="rounded-xl px-3.5 py-2.5 text-sm font-semibold transition {{ request()->routeIs('home') || request()->is('/') ? 'text-indigo-600 dark:text-white bg-indigo-50 dark:bg-slate-800/80' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                            Accueil
                        </a>
                    @endguest

                    @auth
                        @if (Route::has('dashboard'))
                            <a href="{{ route('dashboard') }}" wire:navigate @click="menuOpen = false" class="rounded-xl px-3.5 py-2.5 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-white bg-indigo-50 dark:bg-slate-800/80' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                Mon Espace
                            </a>
                        @endif

                        <a href="{{ route('payments.history') }}" wire:navigate @click="menuOpen = false" class="rounded-xl px-3.5 py-2.5 text-sm font-semibold transition {{ request()->routeIs('payments.history') ? 'text-indigo-600 dark:text-white bg-indigo-50 dark:bg-slate-800/80' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                            Mes paiements
                        </a>

                        @if(auth()->user()->canAccessBackOffice())
                            <div class="px-3.5 pb-1 pt-3 text-[9px] font-black uppercase tracking-[0.2em] text-slate-500">Back-office</div>
                            <a href="{{ route('admin.contributions') }}" wire:navigate @click="menuOpen = false" class="rounded-xl px-3.5 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.contributions') ? 'text-emerald-600 dark:text-white bg-emerald-50 dark:bg-emerald-900/40' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                Valider les apports
                            </a>
                            @if(auth()->user()->canReviewProjects())
                                <a href="{{ route('admin.projects.review') }}" wire:navigate @click="menuOpen = false" class="rounded-xl px-3.5 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.projects.review') ? 'text-indigo-600 dark:text-white bg-indigo-50 dark:bg-slate-800/80' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                    Revoir les projets
                                </a>
                            @endif
                            <a href="{{ Route::has('admin.audit') ? route('admin.audit') : url('/admin/audit') }}" wire:navigate @click="menuOpen = false" class="rounded-xl px-3.5 py-2.5 text-sm font-semibold transition {{ request()->is('admin/audit*') || request()->routeIs('admin.audit') ? 'text-indigo-600 dark:text-white bg-indigo-50 dark:bg-slate-800/80' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                Historique sécurisé
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button type="submit" class="w-full rounded-xl px-3.5 py-2.5 text-left text-sm font-semibold text-rose-600 dark:text-rose-400 transition hover:bg-rose-50 dark:hover:bg-rose-950/40">
                                Déconnexion
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" wire:navigate @click="menuOpen = false" class="mt-1 rounded-xl bg-indigo-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-md shadow-indigo-500/20 transition hover:bg-indigo-500">
                            Connexion
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <div class="flex min-w-0 flex-1 flex-col transition-[padding] duration-300" :class="sidebarOpen ? 'lg:pl-72' : 'lg:pl-20'">

        <!-- En-tête Optionnel ($header) -->
        @if (isset($header))
            <header class="bg-white/50 dark:bg-slate-900/40 border-b border-slate-200/80 dark:border-slate-800/80 backdrop-blur-md py-6 transition-all duration-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        @if(session('error'))
            <div class="mx-auto mt-6 flex w-full max-w-7xl items-start gap-3 rounded-2xl border border-rose-200 dark:border-rose-400/20 bg-rose-50 dark:bg-rose-400/10 px-4 py-3 text-sm font-semibold text-rose-700 dark:text-rose-200 shadow-lg shadow-rose-950/5 dark:shadow-rose-950/10" x-data="{ show: true }" x-show="show" role="alert">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-500 dark:text-rose-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 4.6 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 4.6a2 2 0 0 0-3.4 0Z" /></svg>
                <span class="flex-1">{{ session('error') }}</span>
                <button @click="show = false" type="button" class="text-lg leading-none text-rose-500 dark:text-rose-300 hover:text-rose-700 dark:hover:text-white" aria-label="Fermer">&times;</button>
            </div>
        @endif

        <!-- Contenu de la Page -->
        <main class="flex-grow max-w-7xl w-full mx-auto px-4 pb-28 pt-8 sm:px-6 lg:px-8 lg:pb-8">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md py-8 mt-12 text-center text-xs text-slate-500 font-mono transition-all">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p>© {{ date('Y') }} MUTUALIS — Partage et entraide entre nous.</p>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-indigo-50 dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-slate-800 shadow-sm">
                        Version 2.4
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        Sécurité Active
                    </span>
                </div>
            </div>
        </footer>

        </div>

        <!-- Navigation mobile fixe -->
        <nav class="fixed inset-x-3 bottom-3 z-50 flex items-center justify-around rounded-2xl border border-slate-200 dark:border-white/10 bg-white/90 dark:bg-slate-950/90 px-2 py-2 shadow-2xl shadow-slate-950/10 dark:shadow-black/30 backdrop-blur-2xl lg:hidden" aria-label="Navigation mobile">
            @guest
                <a href="{{ route('home') }}" wire:navigate class="flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl px-2 py-2 text-slate-500 dark:text-slate-400 transition hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white {{ request()->routeIs('home') ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-300' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z" /><path stroke-linecap="round" d="M9 21v-7h6v7" /></svg><span class="text-[10px] font-bold">Accueil</span>
                </a>
            @endguest
            <a href="{{ route('projects.index') }}" wire:navigate class="flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl px-2 py-2 text-slate-500 dark:text-slate-400 transition hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white {{ request()->routeIs('projects.index') ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-300' : '' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M5 5h14v14H5zM8 9h8m-8 3h8m-8 3h5" /></svg><span class="text-[10px] font-bold">Projets</span>
            </a>
            @auth
                <a href="{{ route('dashboard', ['activeTab' => 'contributions']) }}" wire:navigate class="flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl px-2 py-2 text-slate-500 dark:text-slate-400 transition hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white {{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-300' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 3v18m-4-4 4 4 4-4m-8-8 4 4 4-4" /></svg><span class="text-[10px] font-bold">Mes apports</span>
                </a>
                <a href="{{ url('/profile') }}" wire:navigate class="flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl px-2 py-2 text-slate-500 dark:text-slate-400 transition hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white {{ request()->is('profile*') ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-300' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19a4 4 0 0 0-8 0m4-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /></svg><span class="text-[10px] font-bold">Profil</span>
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate class="flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl px-2 py-2 text-slate-500 dark:text-slate-400 transition hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-900 dark:hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4m-4-4 4-4-4-4m4 4H3" /></svg><span class="text-[10px] font-bold">Connexion</span>
                </a>
            @endauth
        </nav>

        @auth
            <livewire:quick-contribution-modal />
            <livewire:notification-center />
        @endauth

        <div
            x-data="{ show: false, message: '' }"
            x-on:notify.window="message = $event.detail.message; show = true; clearTimeout(window.__notifyTimeout); window.__notifyTimeout = setTimeout(() => show = false, 6000)"
            x-show="show"
            x-transition
            x-cloak
            class="fixed inset-x-4 bottom-24 z-[90] mx-auto flex max-w-md items-start gap-3 rounded-2xl border border-emerald-300 dark:border-emerald-400/30 bg-white/95 dark:bg-slate-900/95 px-4 py-3.5 text-sm text-slate-900 dark:text-white shadow-2xl shadow-slate-950/10 dark:shadow-black/40 backdrop-blur-xl lg:inset-x-auto lg:right-8 lg:bottom-8"
        >
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            <span class="flex-1 font-semibold" x-text="message"></span>
            <button @click="show = false" type="button" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white" aria-label="Fermer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" /></svg>
            </button>
        </div>

    </div>
</body>
</html>
