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

    public string $competenceNom = '';

    public string $competenceNiveau = '';

    public string $materielNom = '';

    public string $materielQuantiteDisponible = '1';

    public string $materielUnite = 'unité';

    public string $materielEtat = '';

    public string $materielLocalisation = '';

    public string $materielDisponibleDu = '';

    public string $materielDisponibleAu = '';

    public string $message = '';

    #[On('open-contribution-modal')]
    public function openContributionModal(int $projectId): void
    {
        $this->resetValidation();
        $this->reset([
            'montant',
            'descriptionApport',
            'competenceNom',
            'competenceNiveau',
            'materielNom',
            'materielQuantiteDisponible',
            'materielUnite',
            'materielEtat',
            'materielLocalisation',
            'materielDisponibleDu',
            'materielDisponibleAu',
            'message',
        ]);
        $this->projectId = $projectId;
        $this->typeApport = ContributionType::FINANCIER->value;
        $this->materielQuantiteDisponible = '1';
        $this->materielUnite = 'unité';
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
        $this->reset([
            'montant',
            'descriptionApport',
            'competenceNom',
            'competenceNiveau',
            'materielNom',
            'materielQuantiteDisponible',
            'materielUnite',
            'materielEtat',
            'materielLocalisation',
            'materielDisponibleDu',
            'materielDisponibleAu',
            'message',
        ]);
        $this->materielQuantiteDisponible = '1';
        $this->materielUnite = 'unité';
    }

    public function updated(string $property): void
    {
        if (in_array($property, [
            'projectId',
            'typeApport',
            'montant',
            'descriptionApport',
            'competenceNom',
            'competenceNiveau',
            'materielNom',
            'materielQuantiteDisponible',
            'materielUnite',
            'materielEtat',
            'materielLocalisation',
            'materielDisponibleDu',
            'materielDisponibleAu',
            'message',
        ], true)) {
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

        if (Gate::denies($ability, [MutualizationContribution::class, $project])) {
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
            'competence_nom' => $validated['typeApport'] === ContributionType::COMPETENCE->value
                ? $validated['competenceNom']
                : null,
            'competence_niveau' => $validated['typeApport'] === ContributionType::COMPETENCE->value
                ? ($validated['competenceNiveau'] ?: null)
                : null,
            'materiel_nom' => $validated['typeApport'] === ContributionType::MATERIEL->value
                ? $validated['materielNom']
                : null,
            'materiel_quantite_disponible' => $validated['typeApport'] === ContributionType::MATERIEL->value
                ? $validated['materielQuantiteDisponible']
                : null,
            'materiel_unite' => $validated['typeApport'] === ContributionType::MATERIEL->value
                ? $validated['materielUnite']
                : null,
            'materiel_etat' => $validated['typeApport'] === ContributionType::MATERIEL->value
                ? ($validated['materielEtat'] ?: null)
                : null,
            'materiel_localisation' => $validated['typeApport'] === ContributionType::MATERIEL->value
                ? ($validated['materielLocalisation'] ?: null)
                : null,
            'materiel_disponible_du' => $validated['typeApport'] === ContributionType::MATERIEL->value
                ? ($validated['materielDisponibleDu'] ?: null)
                : null,
            'materiel_disponible_au' => $validated['typeApport'] === ContributionType::MATERIEL->value
                ? ($validated['materielDisponibleAu'] ?: null)
                : null,
            'statut' => ContributionStatus::EN_ATTENTE->value,
        ]);

        $project->user?->notify(new NewContributionReceived($contribution->load('project')));

        $message = $validated['typeApport'] === ContributionType::FINANCIER->value
            ? 'Votre apport financier a été envoyé pour validation. Une fois validé, retrouvez-le dans "Mes contributions" pour signer le contrat et payer.'
            : 'Votre proposition a été envoyée pour validation. Retrouvez son statut dans "Mes contributions".';

        $this->close();
        $this->dispatch('contribution-created');
        $this->dispatch('notify', message: $message);
        session()->flash('contribution-message', $message);
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
                'nullable',
                'string',
                'max:1000',
            ],
            'competenceNom' => [
                Rule::requiredIf(fn (): bool => $this->typeApport === ContributionType::COMPETENCE->value),
                'nullable',
                'string',
                'max:150',
            ],
            'competenceNiveau' => [
                Rule::requiredIf(fn (): bool => $this->typeApport === ContributionType::COMPETENCE->value),
                'nullable',
                'string',
                'max:60',
            ],
            'materielNom' => [
                Rule::requiredIf(fn (): bool => $this->typeApport === ContributionType::MATERIEL->value),
                'nullable',
                'string',
                'max:150',
            ],
            'materielQuantiteDisponible' => [
                Rule::requiredIf(fn (): bool => $this->typeApport === ContributionType::MATERIEL->value),
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],
            'materielUnite' => ['nullable', 'string', 'max:40'],
            'materielEtat' => ['nullable', 'string', 'max:80'],
            'materielLocalisation' => ['nullable', 'string', 'max:255'],
            'materielDisponibleDu' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'materielDisponibleAu' => [
                'nullable',
                'date',
                'after_or_equal:materielDisponibleDu',
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
