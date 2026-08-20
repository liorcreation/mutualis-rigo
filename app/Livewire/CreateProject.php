<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Livewire\Component;

class CreateProject extends Component
{
    /**
     * Étape courante de l'assistant (1 = fiche projet, 2 = RH, 3 = matériel).
     */
    public int $step = 1;

    public string $titre = '';

    public string $categorie = '';

    public string $description = '';

    public string $besoinFinancierTarget = '';

    /**
     * @var list<array{role: string, niveau: string}>
     */
    public array $competences = [
        ['role' => '', 'niveau' => ''],
    ];

    /**
     * @var list<array{nom: string, quantite: string, date_souhaitee: string}>
     */
    public array $materiels = [
        ['nom' => '', 'quantite' => '1', 'date_souhaitee' => ''],
    ];

    public function addCompetence(): void
    {
        $this->competences[] = ['role' => '', 'niveau' => ''];
    }

    public function removeCompetence(int $index): void
    {
        if (count($this->competences) > 1) {
            unset($this->competences[$index]);
            $this->competences = array_values($this->competences);
        }
    }

    public function addMateriel(): void
    {
        $this->materiels[] = ['nom' => '', 'quantite' => '1', 'date_souhaitee' => ''];
    }

    public function removeMateriel(int $index): void
    {
        if (count($this->materiels) > 1) {
            unset($this->materiels[$index]);
            $this->materiels = array_values($this->materiels);
        }
    }

    public function updated(string $property): void
    {
        if (str_starts_with($property, 'competences.') || str_starts_with($property, 'materiels.')) {
            $this->validateOnly($property);
        }

        if (in_array($property, ['titre', 'categorie', 'description', 'besoinFinancierTarget'], true)) {
            $this->validateOnly($property);
        }
    }

    /**
     * Valide uniquement les champs de l'étape affichée avant de passer à la suivante.
     */
    public function nextStep(): void
    {
        $fields = match ($this->step) {
            1 => ['titre', 'categorie', 'description', 'besoinFinancierTarget'],
            2 => ['competences', 'competences.*.role', 'competences.*.niveau'],
            default => [],
        };

        if ($fields !== []) {
            $this->validate(Arr::only($this->rules(), $fields));
        }

        $this->step = min($this->step + 1, 3);
    }

    public function previousStep(): void
    {
        $this->resetValidation();
        $this->step = max($this->step - 1, 1);
    }

    public function save(): void
    {
        $validated = $this->validate();

        Project::create([
            'user_id' => auth()->id(),
            'titre' => $validated['titre'],
            'categorie' => $validated['categorie'],
            'description' => $validated['description'],
            'statut' => ProjectStatus::EN_ETUDE->value,
            'besoin_financier_target' => $validated['besoinFinancierTarget'] ?: null,
            'besoin_financier_actuel' => 0,
            'besoins_competences' => array_values(array_filter(
                $validated['competences'],
                fn (array $competence): bool => $competence['role'] !== ''
            )),
            'besoins_materiels' => array_values(array_filter(array_map(
                fn (array $materiel): ?array => $materiel['nom'] === '' ? null : [
                    'label' => $materiel['nom'],
                    'quantite' => $materiel['quantite'] !== '' ? (int) $materiel['quantite'] : 1,
                    'date_souhaitee' => $materiel['date_souhaitee'] ?: null,
                ],
                $validated['materiels']
            ))),
        ]);

        session()->flash('project-message', 'Votre projet est maintenant soumis à l’étude. Retrouvez-le dans le catalogue.');
        $this->redirect(route('projects.index'), navigate: true);
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'min:5', 'max:150'],
            'categorie' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'besoinFinancierTarget' => ['nullable', 'numeric', 'min:1', 'max:999999999999.99'],
            'competences' => ['array', 'max:20'],
            'competences.*.role' => ['nullable', 'string', 'max:100'],
            'competences.*.niveau' => ['nullable', 'string', 'max:50'],
            'materiels' => ['array', 'max:20'],
            'materiels.*.nom' => ['nullable', 'string', 'max:150'],
            'materiels.*.quantite' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'materiels.*.date_souhaitee' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'titre.required' => 'Le titre du projet est obligatoire.',
            'titre.min' => 'Le titre doit contenir au moins 5 caractères.',
            'categorie.required' => 'Merci de préciser une catégorie.',
            'description.required' => 'La description du projet est obligatoire.',
            'description.min' => 'Décrivez votre projet en au moins 20 caractères.',
            'besoinFinancierTarget.numeric' => 'Le montant cible doit être un nombre.',
            'besoinFinancierTarget.min' => 'Le montant cible doit être supérieur à 0.',
            'materiels.*.date_souhaitee.after_or_equal' => 'La date souhaitée ne peut pas être dans le passé.',
        ];
    }

    public function render(): View
    {
        return view('livewire.create-project')->layout('layouts.app');
    }
}
