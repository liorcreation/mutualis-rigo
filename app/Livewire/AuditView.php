<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\Projet;
use Livewire\Component;

class AuditView extends Component
{
    public string $search = '';

    public string $viewMode = 'blocks'; // 'blocks' (Carnet Cryptographique) ou 'table' (Journal Synthétique)

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function render()
    {
        // Calculs dynamiques pour les KPIs
        $totalProjets = Projet::count();
        $totalBudget = Projet::sum('budget');
        $totalPreuves = AuditLog::count();

        // Requête de recherche sur le journal d'audit
        $query = AuditLog::query()->latest('id');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('action', 'like', '%'.$this->search.'%')
                    ->orWhere('donnees_auditees', 'like', '%'.$this->search.'%')
                    ->orWhere('hash_actuel', 'like', '%'.$this->search.'%')
                    ->orWhere('hash_parent', 'like', '%'.$this->search.'%');
            });
        }

        $auditLogs = $query->get();

        return view('livewire.audit-view', [
            'auditLogs' => $auditLogs,
            'totalProjets' => $totalProjets,
            'totalBudget' => $totalBudget,
            'totalPreuves' => $totalPreuves,
        ]);
    }
}
