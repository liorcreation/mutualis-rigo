<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Notifications\NewContributionReceived;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class QuickContributionModal extends Component
{
    public bool $isOpen = false;

    public ?int $projectId = null;

    public string $typeApport = 'financier';

    public string $montant = '';

    public string $descriptionApport = '';

    public string $message = '';

    #[On('open-contribution-modal')]
    public function openContributionModal(int $projectId): void
    {
        $this->resetValidation();
        $this->reset(['montant', 'descriptionApport', 'message']);
        $this->projectId = $projectId;
        $this->typeApport = ContributionType::FINANCIER->value;
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->resetValidation();
    }

    public function updatedTypeApport(): void
    {
        $this->resetValidation();
        $this->reset(['montant', 'descriptionApport', 'message']);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['projectId', 'typeApport', 'montant', 'descriptionApport', 'message'], true)) {
            $this->validateOnly($property);
        }
    }

    public function save(): void
    {
        $validated = $this->validate();
        $project = Project::findOrFail($validated['projectId']);

        $ability = match ($validated['typeApport']) {
            ContributionType::FINANCIER->value => 'createFinancial',
            ContributionType::MATERIEL->value => 'createMaterial',
            default => 'create',
        };

        if (Gate::denies($ability, [$project])) {
            $this->close();
            session()->flash('error', 'Contribution refusée : votre profil doit être vérifié pour ce type d’apport.');

            return;
        }

        $contribution = MutualizationContribution::create([
            'project_id' => $validated['projectId'],
            'user_id' => auth()->id(),
            'type_apport' => $validated['typeApport'],
            'montant' => $validated['typeApport'] === ContributionType::FINANCIER->value
                ? $validated['montant']
                : null,
            'description_apport' => $this->description($validated),
            'statut' => ContributionStatus::EN_ATTENTE->value,
        ]);

        $project->user?->notify(new NewContributionReceived($contribution->load('project')));

        $this->close();
        $this->dispatch('contribution-created');
        session()->flash('contribution-message', 'Votre proposition a été envoyée pour validation.');
    }

    protected function rules(): array
    {
        return [
            'projectId' => ['required', 'integer', 'exists:projets,id'],
            'typeApport' => [
                'required',
                Rule::in(array_column(ContributionType::cases(), 'value')),
            ],
            'montant' => [
                Rule::requiredIf(fn (): bool => $this->typeApport === ContributionType::FINANCIER->value),
                'nullable',
                'numeric',
                'min:1',
            ],
            'descriptionApport' => [
                Rule::requiredIf(fn (): bool => $this->typeApport === ContributionType::COMPETENCE->value),
                'nullable',
                'string',
                'min:3',
                'max:1000',
            ],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Combine l'expertise proposée et le message facultatif dans le champ métier.
     *
     * @param  array<string, mixed>  $validated
     */
    private function description(array $validated): ?string
    {
        $parts = array_filter([
            $validated['descriptionApport'] ?? null,
            $validated['message'] ?? null,
        ]);

        return $parts === [] ? null : implode("\n\n", $parts);
    }

    public function render()
    {
        return view('livewire.quick-contribution-modal');
    }
}
