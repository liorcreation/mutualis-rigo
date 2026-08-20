<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class SecurityAudit extends Component
{
    use WithPagination;

    public function mount(): void
    {
        Gate::authorize('access-backoffice');
    }

    public function render()
    {
        $auditLogs = AuditLog::query()->latest('id')->paginate(15);
        $totalProjets = Project::count();
        $totalBudget = Project::sum('besoin_financier_target');

        return view('livewire.security-audit', [
            'auditLogs' => $auditLogs,
            'totalProjets' => $totalProjets,
            'totalBudget' => $totalBudget,
        ]);
    }
}
