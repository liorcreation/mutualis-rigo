<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MUTUALIS') }} — Connexion</title>

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

    <!-- Script Anti-Flicker & Synchronisation du Thème -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="font-['Plus_Jakarta_Sans'] antialiased min-h-screen flex flex-col justify-between bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-300">

    <!-- Effets d'ambiance lumineux en arrière-plan -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-600/15 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[32rem] h-[32rem] bg-indigo-500/5 dark:bg-indigo-500/10 rounded-full blur-[140px]"></div>
    </div>

    <div class="relative z-10 flex flex-col min-h-screen justify-between">

        <!-- En-tête -->
        <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between">
            <a href="{{ Route::has('home') ? route('home') : '/' }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-emerald-400 p-0.5 shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-all">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="text-lg font-extrabold tracking-tight text-slate-900 dark:text-white">MUTUALIS</span>
                    <span class="block text-[9px] font-mono tracking-widest text-indigo-600 dark:text-indigo-400 uppercase font-semibold">Registre de Confiance</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ Route::has('home') ? route('home') : '/' }}" class="text-xs font-mono font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Retour à l'accueil</span>
                </a>

                <!-- Bouton Toggle Thème -->
                <button x-data="{
                    isDark: document.documentElement.classList.contains('dark'),
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
                }" @click="toggle()" type="button" class="p-2.5 rounded-xl bg-white/80 dark:bg-slate-900/80 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 shadow-sm backdrop-blur-md transition-all cursor-pointer" :aria-label="isDark ? 'Passer en thème clair' : 'Passer en thème sombre'" title="Basculer de thème">
                    <svg x-show="!isDark" x-cloak class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg x-show="isDark" x-cloak class="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        </header>

        <!-- Zone du formulaire -->
        <main class="flex-grow flex items-center justify-center p-4 sm:p-6 my-auto">
            <div class="w-full max-w-md mx-auto">
                {{ $slot }}
            </div>
        </main>

        <!-- Pied de page -->
        <footer class="py-6 text-center text-xs font-mono text-slate-500">
            © {{ date('Y') }} MUTUALIS — Plateforme d'Intégrité Cryptographique.
        </footer>

    </div>

</body>
</html>
