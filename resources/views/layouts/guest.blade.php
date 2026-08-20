<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
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
</head>
<body class="font-['Plus_Jakarta_Sans'] antialiased min-h-screen flex flex-col justify-between bg-slate-950 text-slate-100">

    <!-- Effets d'ambiance lumineux en arrière-plan -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[32rem] h-[32rem] bg-indigo-500/10 rounded-full blur-[140px]"></div>
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
                    <span class="text-lg font-extrabold tracking-tight text-white">MUTUALIS</span>
                    <span class="block text-[9px] font-mono tracking-widest text-indigo-400 uppercase font-semibold">Registre de Confiance</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ Route::has('home') ? route('home') : '/' }}" class="text-xs font-mono font-medium text-slate-300 hover:text-white transition-colors flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900/80 border border-slate-800 shadow-sm backdrop-blur-md">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Retour à l'accueil</span>
                </a>
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
