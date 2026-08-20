<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\Project;
use Livewire\Component;

class SecurityAudit extends Component
{
    public function render()
    {
        $auditLogs = AuditLog::query()->latest('id')->get();
        $totalProjets = Project::count();
        $totalBudget = Project::sum('besoin_financier_target');

        return view('livewire.security-audit', [
            'auditLogs' => $auditLogs,
            'totalProjets' => $totalProjets,
            'totalBudget' => $totalBudget,
        ]);
    }
}
