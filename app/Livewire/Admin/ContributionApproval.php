<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Models\MutualizationContribution;
use App\Notifications\ContributionStatusUpdated;
use App\Services\ProjectMutualizationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ContributionApproval extends Component
{
    use WithPagination;

    public string $typeFilter = 'all';

    public ?int $selectedContributionId = null;

    public string $decision = '';

    public string $commentaire = '';

    public bool $showReview = false;

    public function mount(): void
    {
        Gate::authorize('access-backoffice');
        abort_if($this->allowedTypes() === [], 403);
    }

    public function updatedTypeFilter(): void
    {
        abort_unless($this->canAccessType($this->typeFilter), 403);
        $this->resetPage();
    }

    public function review(int $contributionId, string $decision): void
    {
        abort_unless(in_array($decision, ['valide', 'refuse'], true), 422);

        $contribution = MutualizationContribution::findOrFail($contributionId);
        abort_unless($this->canAccessType($contribution->type_apport->value), 403);

        $this->selectedContributionId = $contribution->id;
        $this->decision = $decision;
        $this->commentaire = '';
        $this->resetValidation();
        $this->showReview = true;
    }

    public function closeReview(): void
    {
        $this->showReview = false;
        $this->resetValidation();
    }

    public function saveDecision(ProjectMutualizationService $service): void
    {
        $validated = $this->validate();
        $contribution = MutualizationContribution::with('project')->findOrFail($validated['selectedContributionId']);

        abort_unless($this->canAccessType($contribution->type_apport->value), 403);
        abort_unless($contribution->statut === ContributionStatus::EN_ATTENTE, 422);

        $contribution->update([
            'statut' => $validated['decision'],
            'commentaire_validation' => $validated['commentaire'] ?: null,
        ]);

        $contribution->load('project', 'user')->user?->notify(new ContributionStatusUpdated($contribution));

        $service->recalculate($contribution->project);

        $this->closeReview();
        $this->reset(['selectedContributionId', 'decision', 'commentaire']);
        session()->flash('approval-message', $validated['decision'] === 'valide'
            ? 'Contribution validée et progression du projet recalculée.'
            : 'Contribution refusée avec son motif.');
    }

    protected function rules(): array
    {
        return [
            'selectedContributionId' => ['required', 'integer', 'exists:mutualization_contributions,id'],
            'decision' => ['required', Rule::in(['valide', 'refuse'])],
            'commentaire' => [
                Rule::requiredIf(fn (): bool => $this->decision === 'refuse'),
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function render(): View
    {
        $contributions = MutualizationContribution::query()
            ->with(['project', 'user.profile'])
            ->where('statut', ContributionStatus::EN_ATTENTE->value)
            ->whereIn('type_apport', $this->allowedTypes())
            ->when($this->typeFilter !== 'all', fn ($query) => $query->where('type_apport', $this->typeFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.contribution-approval', [
            'contributions' => $contributions,
            'availableTypes' => $this->allowedTypes(),
        ])->layout('layouts.app');
    }

    private function canAccessType(string $type): bool
    {
        return $type === 'all' || in_array($type, $this->allowedTypes(), true);
    }

    /**
     * @return list<string>
     */
    private function allowedTypes(): array
    {
        $role = auth()->user()?->role;

        return $role ? ContributionType::allowedFor($role) : [];
    }
}
