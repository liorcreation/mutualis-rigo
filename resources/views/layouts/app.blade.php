<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
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

    <!-- FormKit AutoAnimate (Fluidifie automatiquement l'ajout/suppression dans Livewire) -->
    <script src="https://cdn.jsdelivr.net/npm/@formkit/auto-animate@0.7.0/index.min.js"></script>
</head>
<body class="font-['Plus_Jakarta_Sans'] antialiased h-full text-slate-100 bg-slate-950 selection:bg-indigo-500 selection:text-white">

    <div x-data="{ sidebarOpen: true }" class="min-h-screen flex flex-col bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-slate-950 to-black">

        <!-- Sidebar desktop rétractable -->
        <aside class="fixed inset-y-0 left-0 z-50 hidden flex-col border-r border-slate-800/80 bg-slate-950/90 px-3 py-5 shadow-2xl shadow-black/20 backdrop-blur-2xl transition-all duration-300 lg:flex" :class="sidebarOpen ? 'w-72' : 'w-20'">
            <div class="flex items-center justify-between px-3">
                <a href="{{ auth()->check() ? route('projects.index') : route('home') }}" wire:navigate class="flex items-center gap-3 overflow-hidden">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-emerald-400 text-white shadow-lg shadow-indigo-500/20">✦</div>
                    <div x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                        <span class="block text-lg font-black tracking-tight text-white">RIGO</span>
                        <span class="block text-[9px] font-bold uppercase tracking-[0.2em] text-indigo-400">Mutualisation</span>
                    </div>
                </a>
                <button @click="sidebarOpen = !sidebarOpen" type="button" class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-800 hover:text-indigo-400" aria-label="Rétracter la sidebar">
                    <svg class="h-4 w-4 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" /></svg>
                </button>
            </div>

            <nav class="mt-10 flex-1 space-y-2">
                @guest
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-400 transition hover:bg-indigo-950/40 hover:text-indigo-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z" /><path stroke-linecap="round" d="M9 21v-7h6v7" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Accueil</span>
                    </a>
                @endguest
                <a href="{{ route('projects.index') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-400 transition hover:bg-indigo-950/40 hover:text-indigo-300">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5v-11Z" /><path stroke-linecap="round" d="M8 8h8m-8 4h8m-8 4h5" /></svg>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Catalogue projets</span>
                </a>
                @auth
                    <a href="{{ route('dashboard', ['activeTab' => 'contributions']) }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-400 transition hover:bg-indigo-950/40 hover:text-indigo-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-4-4 4 4 4-4m-8-8 4 4 4-4" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Mes contributions</span>
                    </a>
                    <a href="{{ route('payments.history') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-400 transition hover:bg-indigo-950/40 hover:text-indigo-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h5M4 6h16a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Mes paiements</span>
                    </a>
                    <a href="{{ route('material.reservations') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-400 transition hover:bg-indigo-950/40 hover:text-indigo-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Réservation matériel</span>
                    </a>
                    <a href="{{ url('/profile') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-400 transition hover:bg-indigo-950/40 hover:text-indigo-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19a4 4 0 0 0-8 0m4-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm8 8a3 3 0 0 0-5.5-1.7M17 11a2.5 2.5 0 1 0-1.5-4.5" /></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Mon profil</span>
                    </a>
                    @if(auth()->user()->canAccessBackOffice())
                        <div x-show="sidebarOpen" x-transition class="px-3 pb-1 pt-7 text-[9px] font-black uppercase tracking-[0.2em] text-slate-500">Back-office</div>
                        <a href="{{ route('admin.contributions') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-400 transition hover:bg-emerald-950/40 hover:text-emerald-300">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 3v18m-4-4 4 4 4-4m-8-8 4 4 4-4" /></svg>
                            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Valider les apports</span>
                        </a>
                        @if(auth()->user()->canReviewProjects())
                            <a href="{{ route('admin.projects.review') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-400 transition hover:bg-indigo-950/40 hover:text-indigo-300">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5h14v14H5zM8 9h8m-8 3h5" /></svg>
                                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Revoir les projets</span>
                            </a>
                        @endif
                        <a href="{{ Route::has('admin.audit') ? route('admin.audit') : url('/admin/audit') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-slate-400 transition hover:bg-indigo-950/40 hover:text-indigo-300">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 0 12 2.944a11.955 11.955 0 0 0-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016Z" /></svg>
                            <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Historique sécurisé</span>
                        </a>
                    @endif
                @endauth
            </nav>

            @auth
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold text-rose-400 transition hover:bg-rose-950/40">
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

            <div x-show="sidebarOpen" x-transition class="mt-2 rounded-2xl border border-indigo-400/20 bg-indigo-500/10 p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-300">RIGO Connect</p>
                <p class="mt-2 text-xs leading-5 text-slate-400">Mutualisez vos ressources, accélérez les projets.</p>
            </div>
        </aside>

        <!-- Barre de Navigation Principale -->
        <nav class="sticky top-0 z-50 backdrop-blur-xl bg-slate-950/75 border-b border-slate-800/80 transition-all duration-300 shadow-sm lg:hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">

                    <!-- Logo MUTUALIS -->
                    <div class="flex items-center gap-4">
                        <a href="{{ auth()->check() ? route('projects.index') : (Route::has('home') ? route('home') : '/') }}" class="flex items-center gap-3 cursor-pointer select-none group" wire:navigate>
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-emerald-400 p-0.5 shadow-lg shadow-indigo-500/20 group-hover:shadow-indigo-500/40 group-hover:scale-105 transition-all duration-300">
                                <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-400 group-hover:rotate-12 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <span class="text-xl font-extrabold tracking-tight text-white group-hover:text-indigo-400 transition-colors">MUTUALIS</span>
                                <span class="block text-[10px] font-mono tracking-widest text-indigo-400 uppercase font-semibold">Espace de Partage</span>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        @guest
                            <a href="{{ Route::has('home') ? route('home') : '/' }}" wire:navigate class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-105 {{ request()->routeIs('home') || request()->is('/') ? 'text-white bg-slate-800/80 border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:text-white' }}">
                                Accueil
                            </a>
                        @endguest

                        @if (Route::has('projects.index'))
                            <a href="{{ route('projects.index') }}" wire:navigate class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-105 {{ request()->routeIs('projects.index') ? 'text-white bg-slate-800/80 border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:text-white' }}">
                                Demandes
                            </a>
                        @endif

                        @auth
                            @if (Route::has('dashboard'))
                                <a href="{{ route('dashboard') }}" wire:navigate class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-105 {{ request()->routeIs('dashboard') ? 'text-white bg-slate-800/80 border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:text-white' }}">
                                    Mon Espace
                                </a>
                            @endif

                            <a href="{{ Route::has('admin.audit') ? route('admin.audit') : (Route::has('audit') ? route('audit') : url('/admin/audit')) }}" wire:navigate class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-105 {{ request()->is('admin/audit*') || request()->routeIs('admin.audit') || request()->routeIs('audit') ? 'text-white bg-slate-800/80 border border-slate-700/50 shadow-sm' : 'text-slate-400 hover:text-white' }}">
                                Historique Sécurisé
                            </a>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-red-400 hover:bg-red-950/40 border border-transparent hover:border-red-900/50 transition-all hover:scale-105 cursor-pointer">
                                    Déconnexion
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" wire:navigate class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-500 shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:scale-105 transition-all">
                                Connexion
                            </a>
                        @endauth

                    </div>

                </div>
            </div>
        </nav>

        <div class="flex min-w-0 flex-1 flex-col transition-[padding] duration-300" :class="sidebarOpen ? 'lg:pl-72' : 'lg:pl-20'">

        <!-- En-tête Optionnel ($header) -->
        @if (isset($header))
            <header class="bg-slate-900/40 border-b border-slate-800/80 backdrop-blur-md py-6 transition-all duration-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        @if(session('error'))
            <div class="mx-auto mt-6 flex w-full max-w-7xl items-start gap-3 rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm font-semibold text-rose-200 shadow-lg shadow-rose-950/10" x-data="{ show: true }" x-show="show" role="alert">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 4.6 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 4.6a2 2 0 0 0-3.4 0Z" /></svg>
                <span class="flex-1">{{ session('error') }}</span>
                <button @click="show = false" type="button" class="text-lg leading-none text-rose-300 hover:text-white" aria-label="Fermer">&times;</button>
            </div>
        @endif

        <!-- Contenu de la Page -->
        <main class="flex-grow max-w-7xl w-full mx-auto px-4 pb-28 pt-8 sm:px-6 lg:px-8 lg:pb-8">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-800/80 bg-slate-950/80 backdrop-blur-md py-8 mt-12 text-center text-xs text-slate-500 font-mono transition-all">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p>© {{ date('Y') }} MUTUALIS — Partage et entraide entre nous.</p>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-900 text-indigo-400 border border-slate-800 shadow-sm">
                        Version 2.4
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-400 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        Sécurité Active
                    </span>
                </div>
            </div>
        </footer>

        </div>

        <!-- Navigation mobile fixe -->
        <nav class="fixed inset-x-3 bottom-3 z-50 flex items-center justify-around rounded-2xl border border-white/10 bg-slate-950/90 px-2 py-2 shadow-2xl shadow-black/30 backdrop-blur-2xl lg:hidden" aria-label="Navigation mobile">
            @guest
                <a href="{{ route('home') }}" wire:navigate class="flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl px-2 py-2 text-slate-400 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('home') ? 'bg-indigo-500/15 text-indigo-300' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z" /><path stroke-linecap="round" d="M9 21v-7h6v7" /></svg><span class="text-[10px] font-bold">Accueil</span>
                </a>
            @endguest
            <a href="{{ route('projects.index') }}" wire:navigate class="flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl px-2 py-2 text-slate-400 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('projects.index') ? 'bg-indigo-500/15 text-indigo-300' : '' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M5 5h14v14H5zM8 9h8m-8 3h8m-8 3h5" /></svg><span class="text-[10px] font-bold">Projets</span>
            </a>
            @auth
                <a href="{{ route('projects.index') }}" wire:navigate class="flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl px-2 py-2 text-slate-400 transition hover:bg-white/10 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 3v18m-4-4 4 4 4-4m-8-8 4 4 4-4" /></svg><span class="text-[10px] font-bold">Mes apports</span>
                </a>
                <a href="{{ url('/profile') }}" wire:navigate class="flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl px-2 py-2 text-slate-400 transition hover:bg-white/10 hover:text-white {{ request()->is('profile*') ? 'bg-indigo-500/15 text-indigo-300' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19a4 4 0 0 0-8 0m4-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /></svg><span class="text-[10px] font-bold">Profil</span>
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate class="flex min-w-0 flex-1 flex-col items-center gap-1 rounded-xl px-2 py-2 text-slate-400 transition hover:bg-white/10 hover:text-white">
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
            class="fixed inset-x-4 bottom-24 z-[90] mx-auto flex max-w-md items-start gap-3 rounded-2xl border border-emerald-400/30 bg-slate-900/95 px-4 py-3.5 text-sm text-white shadow-2xl shadow-black/40 backdrop-blur-xl lg:inset-x-auto lg:right-8 lg:bottom-8"
        >
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            <span class="flex-1 font-semibold" x-text="message"></span>
            <button @click="show = false" type="button" class="text-slate-400 hover:text-white" aria-label="Fermer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" /></svg>
            </button>
        </div>

    </div>
</body>
</html>
