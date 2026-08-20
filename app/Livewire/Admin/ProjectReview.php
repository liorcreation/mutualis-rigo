<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Notifications\ProjectStatusUpdated;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectReview extends Component
{
    use WithPagination;

    public ?int $selectedProjectId = null;

    public string $targetStatus = '';

    public bool $showTransition = false;

    public function mount(): void
    {
        abort_unless($this->canAccess(), 403);
    }

    public function openTransition(int $projectId): void
    {
        $project = Project::findOrFail($projectId);
        abort_unless($this->canTransition($project->statut), 403);

        $this->selectedProjectId = $project->id;
        $this->targetStatus = $project->statut === ProjectStatus::EN_ETUDE
            ? ProjectStatus::EN_COURS_DE_MUTUALISATION->value
            : ProjectStatus::CLOTURE->value;
        $this->resetValidation();
        $this->showTransition = true;
    }

    public function closeTransition(): void
    {
        $this->showTransition = false;
        $this->resetValidation();
    }

    public function updateStatus(): void
    {
        $validated = $this->validate();
        $project = Project::findOrFail($validated['selectedProjectId']);

        abort_unless($this->canTransition($project->statut), 403);
        abort_unless($project->statut->allowedTransitions()->contains($validated['targetStatus']), 422);

        $project->update(['statut' => $validated['targetStatus']]);
        $project->user?->notify(new ProjectStatusUpdated($project));

        $this->closeTransition();
        $this->reset(['selectedProjectId', 'targetStatus']);
        session()->flash('review-message', 'Le statut du projet a été mis à jour.');
    }

    protected function rules(): array
    {
        return [
            'selectedProjectId' => ['required', 'integer', 'exists:projets,id'],
            'targetStatus' => [Rule::in(array_column(ProjectStatus::cases(), 'value'))],
        ];
    }

    public function render(): View
    {
        abort_unless($this->canAccess(), 403);

        return view('livewire.admin.project-review', [
            'projects' => Project::query()
                ->with('user.profile')
                ->whereIn('statut', [
                    ProjectStatus::EN_ETUDE->value,
                    ProjectStatus::EN_COURS_DE_MUTUALISATION->value,
                    ProjectStatus::CLOTURE->value,
                ])
                ->latest()
                ->paginate(10),
        ])->layout('layouts.app');
    }

    private function canAccess(): bool
    {
        return (bool) auth()->user()?->canReviewProjects();
    }

    private function canTransition(ProjectStatus $status): bool
    {
        return $status->allowedTransitions()->isNotEmpty();
    }
}
