<?php

namespace App\Livewire;

use App\Enums\ProjectStatus;
use App\Models\AuditLog;
use App\Models\Projet;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        // 1. Calcul des statistiques en temps réel depuis la base de données
        $demandesEnAttente = Projet::where('statut', ProjectStatus::EN_ETUDE->value)->count();
        $chosesPartagees = Projet::where('statut', ProjectStatus::EN_COURS_DE_MUTUALISATION->value)->count();
        $totalAide = AuditLog::count(); // Ou autre métrique de ton choix

        // 2. Récupération des 3 dernières activités
        $dernieresActivites = Projet::latest()->take(3)->get();

        return view('livewire.dashboard-stats', [
            'demandesEnAttente' => $demandesEnAttente,
            'chosesPartagees' => $chosesPartagees,
            'totalAide' => $totalAide,
            'dernieresActivites' => $dernieresActivites,
        ]);
    }
}
