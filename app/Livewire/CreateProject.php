<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateProject extends Component
{
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
     * @var list<string>
     */
    public array $materiels = [''];

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
        $this->materiels[] = '';
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
            'besoins_materiels' => array_values(array_filter(
                $validated['materiels'],
                fn (string $materiel): bool => trim($materiel) !== ''
            )),
        ]);

        session()->flash('project-message', 'Votre projet est maintenant soumis à l’étude.');
        $this->redirect(route('dashboard'), navigate: true);
    }

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
            'materiels.*' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function render(): View
    {
        return view('livewire.create-project')->layout('layouts.app');
    }
}
