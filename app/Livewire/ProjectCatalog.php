<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Services\ProjectMutualizationService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectCatalog extends Component
{
    use WithPagination;

    public string $search = '';

    public string $needType = 'all';

    public string $status = 'all';

    public int $perPage = 9;

    /**
     * Conserve les filtres dans l'URL pour permettre le partage d'une vue.
     *
     * @var array<string, array<string, string>>
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'needType' => ['except' => 'all'],
        'status' => ['except' => 'all'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedNeedType(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'needType', 'status']);
        $this->resetPage();
    }

    public function render(ProjectMutualizationService $service): View
    {
        $projects = Project::query()
            ->with([
                'user.profile',
                'contributions' => fn ($query) => $query->where('statut', 'valide'),
                'contributions.contract',
            ])
            ->when($this->search !== '', function ($query): void {
                $search = '%'.trim($this->search).'%';

                $query->where(function ($query) use ($search): void {
                    $query->where('titre', 'like', $search)
                        ->orWhere('description', 'like', $search)
                        ->orWhere('categorie', 'like', $search);
                });
            })
            ->when($this->status !== 'all', function ($query): void {
                $query->where('statut', $this->status);
            })
            ->when($this->needType !== 'all', function ($query): void {
                match ($this->needType) {
                    'financier' => $query->where(function ($query): void {
                        $query->where('besoin_financier_target', '>', 0)
                            ->orWhere('budget', '>', 0);
                    }),
                    'competence' => $query->whereJsonLength('besoins_competences', '>', 0),
                    'materiel' => $query->where(function ($query): void {
                        $query->whereJsonLength('besoins_materiels', '>', 0)
                            ->orWhereNotNull('materiel_requis');
                    }),
                    default => null,
                };
            })
            ->latest()
            ->paginate($this->perPage);

        $progressions = $projects->getCollection()->mapWithKeys(
            fn (Project $project): array => [$project->id => $service->progress($project)]
        );

        return view('livewire.project-catalog', [
            'projects' => $projects,
            'progressions' => $progressions,
            'statuses' => ProjectStatus::cases(),
        ])->layout('layouts.app');
    }
}
