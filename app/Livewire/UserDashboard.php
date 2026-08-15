<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ContributionStatus;
use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Services\ProjectMutualizationService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class UserDashboard extends Component
{
    use WithPagination;

    public string $activeTab = 'projects';

    protected $queryString = [
        'activeTab' => ['except' => 'projects'],
    ];

    public function updatedActiveTab(): void
    {
        $this->resetPage();
    }

    public function selectTab(string $tab): void
    {
        if (in_array($tab, ['projects', 'contributions'], true)) {
            $this->activeTab = $tab;
            $this->resetPage();
        }
    }

    public function render(ProjectMutualizationService $service): View
    {
        $projects = Project::query()
            ->where('user_id', auth()->id())
            ->with(['contributions', 'contributions.contract'])
            ->latest()
            ->paginate(6, pageName: 'projectsPage');

        $contributions = MutualizationContribution::query()
            ->where('user_id', auth()->id())
            ->with(['project', 'contract'])
            ->latest()
            ->paginate(6, pageName: 'contributionsPage');

        $validatedContributions = MutualizationContribution::query()
            ->where('user_id', auth()->id())
            ->where('statut', ContributionStatus::VALIDE->value)
            ->count();

        $pendingContributions = MutualizationContribution::query()
            ->where('user_id', auth()->id())
            ->where('statut', ContributionStatus::EN_ATTENTE->value)
            ->count();

        $progressions = $projects->getCollection()->mapWithKeys(
            fn (Project $project): array => [$project->id => $service->progress($project)]
        );

        return view('livewire.user-dashboard', [
            'projects' => $projects,
            'contributions' => $contributions,
            'progressions' => $progressions,
            'contributionStatuses' => ContributionStatus::cases(),
            'validatedContributions' => $validatedContributions,
            'pendingContributions' => $pendingContributions,
        ])->layout('layouts.app');
    }
}
