<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\ProjectUserAssignment;
use App\Models\User;
use App\Services\HumanResourceAllocationService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Tableau de pilotage de la charge RH.
 */
class ResourceAllocation extends Component
{
    use WithPagination;

    public ?int $projectId = null;

    public ?int $userId = null;

    public string $roleRecherche = '';

    public int $percentageLoad = 20;

    public string $startDate = '';

    public string $endDate = '';

    public function mount(): void
    {
        abort_unless($this->canManage(), 403);
        $this->startDate = today()->toDateString();
    }

    public function save(HumanResourceAllocationService $allocations): void
    {
        $validated = $this->validate();

        $allocations->assign(
            Project::query()->findOrFail($validated['projectId']),
            User::query()->findOrFail($validated['userId']),
            (int) $validated['percentageLoad'],
            today()->createFromFormat('Y-m-d', $validated['startDate']),
            $validated['endDate'] !== ''
                ? today()->createFromFormat('Y-m-d', $validated['endDate'])
                : null,
            $validated['roleRecherche'] !== '' ? $validated['roleRecherche'] : null,
        );

        $this->reset(['projectId', 'userId', 'roleRecherche', 'endDate']);
        $this->percentageLoad = 20;
        $this->startDate = today()->toDateString();
        $this->resetPage();
        session()->flash('allocation-message', 'Affectation enregistrée : la capacité RH reste sous contrôle.');
    }

    public function delete(int $assignmentId, HumanResourceAllocationService $allocations): void
    {
        abort_unless($this->canManage(), 403);
        $allocations->remove(ProjectUserAssignment::query()->findOrFail($assignmentId));
        session()->flash('allocation-message', 'Affectation retirée.');
    }

    protected function rules(): array
    {
        return [
            'projectId' => ['required', 'integer', 'exists:projets,id'],
            'userId' => ['required', 'integer', 'exists:users,id'],
            'roleRecherche' => ['nullable', 'string', 'max:120'],
            'percentageLoad' => ['required', 'integer', 'between:1,100'],
            'startDate' => ['required', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
        ];
    }

    public function render(): View
    {
        abort_unless($this->canManage(), 403);

        $internalRoles = [
            UserRole::COLLABORATEUR->value,
            UserRole::CHEF_PROJET->value,
            UserRole::RESPONSABLE_RH->value,
            UserRole::RESPONSABLE_FINANCIER->value,
            UserRole::TOP_MANAGEMENT->value,
        ];

        return view('livewire.admin.resource-allocation', [
            'projects' => Project::query()->orderBy('titre')->get(['id', 'titre']),
            'users' => User::query()->whereIn('role', $internalRoles)->orderBy('name')->get(['id', 'name', 'email']),
            'assignments' => ProjectUserAssignment::query()
                ->with(['project', 'user'])
                ->latest()
                ->paginate(12),
        ])->layout('layouts.app');
    }

    private function canManage(): bool
    {
        return (bool) auth()->user()?->role?->canManageResourceAllocations();
    }
}
