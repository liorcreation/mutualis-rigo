<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditLog;
use App\Models\Projet;

class SecurityAudit extends Component
{
    public function render()
    {
        // Récupération sécurisée des données avec valeurs par défaut
        $auditLogs = class_exists(AuditLog::class) ? AuditLog::latest()->get() : collect();
        $totalProjets = class_exists(Projet::class) ? Projet::count() : 0;
        $totalBudget = class_exists(Projet::class) ? Projet::sum('budget') : 0;

        return view('livewire.security-audit', [
            'auditLogs'    => $auditLogs,
            'totalProjets' => $totalProjets,
            'totalBudget'  => $totalBudget,
        ]);
    }
}