<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Projet;
use App\Models\TraceAudit;

new class extends Component {
    // Propriétés du formulaire
    public $titre;
    public $description;
    public $budget;
    public $materiel_requis; // <--- AJOUT DU 3ÈME PILIER (Matériel)
    public $selectedUserId;

    // Règles de validation
    protected $rules = [
        'titre' => 'required|min:5|max:100',
        'description' => 'required|min:10',
        'budget' => 'required|numeric|min:1000',
        'materiel_requis' => 'nullable|string|max:1000', // Optionnel mais validé s'il est rempli
        'selectedUserId' => 'required|exists:users,id',
    ];

    public function mount()
    {
        $premierUser = User::first();
        if ($premierUser) {
            $this->selectedUserId = $premierUser->id;
        }
    }

    public function submit()
    {
        $this->validate();

        // Création du projet (L'Observer gère la trace d'audit SHA-256 en tâche de fond)
        Projet::create([
            'titre' => $this->titre,
            'description' => $this->description,
            'budget' => $this->budget,
            'materiel_requis' => $this->materiel_requis, // <--- Enregistrement du matériel requis
            'user_id' => $this->selectedUserId,
            'statut' => 'brouillon',
        ]);

        session()->flash('message', '🎉 Projet publié avec succès ! La trace d\'audit cryptographique a été scellée dans le registre.');

        // Réinitialisation complète du formulaire
        $this->reset(['titre', 'description', 'budget', 'materiel_requis']);
    }
};
?>

@php
    $users = \App\Models\User::all();
    $projets = \App\Models\Projet::with('user')->latest()->get();
    $audits = \App\Models\TraceAudit::with('user')->latest()->get();
@endphp

<div class="min-h-screen bg-slate-50/50 py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased text-slate-900">
    <div class="max-w-7xl mx-auto space-y-10">
        
        <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-8 sm:p-10 shadow-xl border border-slate-800">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-indigo-500/10 via-transparent to-transparent"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 text-[10px] font-bold uppercase tracking-widest rounded-full border border-indigo-500/30">
                            PROJET DE RECHERCHE DE LICENCE
                        </span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-100 to-indigo-200 bg-clip-text text-transparent">
                        MUTUALIS
                    </h1>
                    <p class="text-slate-400 text-sm mt-1 max-w-2xl">
                        Plateforme sécurisée de mutualisation des ressources humaines, financières et matérielles (RIGO-TECH).
                    </p>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-slate-900/80 backdrop-blur-sm rounded-full border border-slate-800 self-start sm:self-auto shadow-inner">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-semibold text-emerald-400 tracking-wider">SYSTÈME ACTIF</span>
                </div>
            </div>
        </div>

        <div class="bg-amber-50/70 border border-amber-200/60 rounded-2xl p-6 shadow-sm backdrop-blur-sm">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-amber-100 text-amber-800 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-sm font-bold text-amber-900">Console de Simulation d'Acteur (Outil de Soutenance)</h3>
                    <p class="text-xs text-amber-700/90 leading-relaxed">
                        Ce sélecteur vous permet de simuler la connexion de différents profils d'utilisateurs. Idéal pour prouver au jury le cloisonnement des rôles et la traçabilité nominative de l'audit cryptographique.
                    </p>
                    <div class="mt-3 max-w-sm">
                        <select wire:model.live="selectedUserId" class="w-full text-xs rounded-xl border-amber-300 py-2.5 px-3 bg-white shadow-sm focus:border-amber-500 focus:ring-amber-500 text-slate-800 font-medium cursor-pointer">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    👤 {{ $user->name }} — [{{ strtoupper(str_replace('_', ' ', $user->role->value)) }}]
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="rounded-2xl bg-emerald-50 p-4 border border-emerald-200 text-sm font-semibold text-emerald-800 shadow-sm flex items-center gap-3 animate-fade-in">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-5 bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <h2 class="text-lg font-bold text-slate-900 mb-6 border-b border-slate-100 pb-4 flex items-center gap-3">
                    <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </span>
                    Publier une Demande de Mutualisation
                </h2>
                
                <form wire:submit="submit" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Titre du Projet</label>
                        <input type="text" wire:model="titre" placeholder="ex: Projet Éolien Communautaire" class="w-full rounded-xl border-slate-200 shadow-sm text-sm p-3 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                        @error('titre') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">1. Ressources Humaines requises</span>
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[9px] font-bold uppercase rounded-full">Humain</span>
                        </div>
                        <textarea wire:model="description" rows="3" placeholder="Quelles compétences recherchez-vous ? (ex: Développeur Laravel, Électricien certifié...)" class="w-full rounded-xl border-slate-200 shadow-sm text-sm p-3 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">2. Ressources Financières nécessaires</span>
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[9px] font-bold uppercase rounded-full">Financier</span>
                        </div>
                        <div class="relative">
                            <input type="number" wire:model="budget" placeholder="Estimation du capital requis" class="w-full rounded-xl border-slate-200 shadow-sm text-sm p-3 pr-16 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-xs font-bold text-slate-400">FCFA</span>
                            </div>
                        </div>
                        @error('budget') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">3. Ressources Matérielles requises</span>
                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[9px] font-bold uppercase rounded-full">Matériel</span>
                        </div>
                        <textarea wire:model="materiel_requis" rows="3" placeholder="De quels équipements avez-vous besoin ? (ex: 3 serveurs NAS, un véhicule utilitaire, locaux commerciaux...)" class="w-full rounded-xl border-slate-200 shadow-sm text-sm p-3 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"></textarea>
                        @error('materiel_requis') <span class="text-red-500 text-xs mt-1.5 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Signer et Publier (SHA-256)
                    </button>
                </form>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100 h-full hover:shadow-md transition-shadow">
                    <h2 class="text-lg font-bold text-slate-900 mb-6 border-b border-slate-100 pb-4 flex items-center gap-3">
                        <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </span>
                        Registre Public des Besoins Mutualisés
                    </h2>
                    
                    <div class="overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="min-w-full divide-y divide-slate-100 text-sm">
                            <thead class="bg-slate-50/70">
                                <tr>
                                    <th class="px-5 py-3.5 text-left font-bold text-slate-500 uppercase tracking-wider text-[11px]">Auteur du Projet</th>
                                    <th class="px-5 py-3.5 text-left font-bold text-slate-500 uppercase tracking-wider text-[11px]">Détail du Besoin</th>
                                    <th class="px-5 py-3.5 text-left font-bold text-slate-500 uppercase tracking-wider text-[11px]">Financement</th>
                                    <th class="px-5 py-3.5 text-center font-bold text-slate-500 uppercase tracking-wider text-[11px]">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($projets as $p)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-5 py-4">
                                            <span class="font-bold text-slate-800 block text-xs">{{ $p->user->name }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono tracking-tight">{{ $p->user->email }}</span>
                                        </td>
                                        
                                        <td class="px-5 py-4">
                                            <div class="space-y-1">
                                                <span class="font-bold text-slate-700 text-xs block">{{ $p->titre }}</span>
                                                <p class="text-xs text-slate-500 line-clamp-2" title="{{ $p->description }}">{{ $p->description }}</p>
                                                
                                                <div class="flex flex-wrap gap-1.5 pt-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                                        👥 RH
                                                    </span>
                                                    @if($p->budget > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                            💰 Financier
                                                        </span>
                                                    @endif
                                                    @if(!empty($p->materiel_requis))
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100" title="{{ $p->materiel_requis }}">
                                                            📦 Matériel
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <span class="text-indigo-600 font-mono font-bold text-xs">
                                                {{ number_format($p->budget, 0, ',', ' ') }} FCFA
                                            </span>
                                        </td>
                                        
                                        <td class="px-5 py-4 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-150">
                                                {{ ucfirst(str_replace('_', ' ', $p->statut->value)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-12 text-center text-slate-400 italic">
                                            <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v4.5m16 0h-16" />
                                            </svg>
                                            Aucun besoin publié pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 text-slate-100 p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-800 font-mono relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,_var(--tw-gradient-stops))] from-indigo-900/10 via-transparent to-transparent"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-4 mb-6 gap-4">
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-slate-800 text-indigo-400 rounded-xl border border-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-200">
                            🛡️ Registre de Sécurité Cryptographique (Chaînage d'Audit)
                        </h3>
                        <p class="text-[10px] text-slate-500 mt-0.5">
                            Chaque bloc dépend mathématiquement du hash du bloc parent en SHA-256.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 px-3 py-1 bg-emerald-500/10 rounded-lg border border-emerald-500/20">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[9px] text-emerald-400 font-bold uppercase tracking-wider">Scellé & Inviolable</span>
                </div>
            </div>

            <div class="space-y-4 max-h-96 overflow-y-auto pr-2 relative z-10">
                @forelse($audits as $audit)
                    <div class="bg-slate-950 p-4 sm:p-5 rounded-2xl border border-slate-800 hover:border-slate-700 transition-all text-xs group">
                        <div class="flex flex-col sm:flex-row sm:justify-between border-b border-slate-900 pb-3 mb-3 gap-2 text-[10px] text-slate-500">
                            <span class="flex items-center gap-1.5 font-bold">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                BLOC D'AUDIT #{{ $audit->id }}
                            </span>
                            <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-400 rounded border border-indigo-500/20 uppercase font-bold text-[9px]">
                                ACTION : {{ $audit->action }}
                            </span>
                            <span class="text-slate-600">{{ $audit->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px] py-1 text-slate-300">
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold mb-0.5">Acteur :</span> 
                                <span class="text-slate-200 font-medium">{{ $audit->user->name }}</span> 
                                <span class="text-slate-500 text-[10px] block">{{ $audit->user->email }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold mb-0.5">Donnée Auditée :</span> 
                                <span class="bg-slate-900 px-2 py-1 rounded text-indigo-300 text-[10px] border border-slate-800 inline-block">
                                    📁 {{ $audit->table_concernee }} (ID Enregistrement: #{{ $audit->enregistrement_id }})
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-t border-slate-900 space-y-2 font-mono text-[10px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1.5">
                                <span class="text-amber-500 font-extrabold w-32 shrink-0 flex items-center gap-1">
                                    ⛓️ HASH PARENT :
                                </span>
                                <span class="text-slate-400 bg-slate-900/60 px-2.5 py-1 rounded select-all truncate block w-full border border-slate-900 font-mono tracking-tight" title="{{ $audit->hash_precedent }}">
                                    {{ $audit->hash_precedent ?? 'INITIAL_ROOT_BLOCK (Genesis)' }}
                                </span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1.5">
                                <span class="text-emerald-400 font-extrabold w-32 shrink-0 flex items-center gap-1">
                                    🔑 HASH ACTUEL :
                                </span>
                                <span class="text-emerald-300 bg-emerald-950/20 px-2.5 py-1 rounded select-all truncate block w-full border border-emerald-500/10 font-mono tracking-tight group-hover:bg-emerald-950/30 transition-colors" title="{{ $audit->hash }}">
                                    {{ $audit->hash }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-500 italic text-xs">
                        <svg class="w-12 h-12 mx-auto text-slate-800 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Le registre d'audit est vide. Publiez votre premier projet pour initier la chaîne cryptographique.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
