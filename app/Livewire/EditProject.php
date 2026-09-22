<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class EditProject extends Component
{
    public Project $project;

    public int $step = 1;

    public string $titre = '';

    public string $categorie = '';

    public string $description = '';

    public string $besoinFinancierTarget = '';

    /** @var list<array{role: string, niveau: string}> */
    public array $competences = [];

    /** @var list<array{nom: string, quantite: string, date_souhaitee: string}> */
    public array $materiels = [];

    public function mount(Project $project): void
    {
        Gate::authorize('update', $project);

        $this->project = $project;
        $this->titre = (string) $project->titre;
        $this->categorie = (string) $project->categorie;
        $this->description = (string) $project->description;
        $this->besoinFinancierTarget = $project->besoin_financier_target !== null
            ? (string) $project->besoin_financier_target
            : '';
        $this->competences = $this->normaliseCompetences($project->besoins_competences ?? []);
        $this->materiels = $this->normaliseMateriels($project->besoins_materiels ?? []);

        if ($this->competences === []) {
            $this->competences = [['role' => '', 'niveau' => '']];
        }

        if ($this->materiels === []) {
            $this->materiels = [['nom' => '', 'quantite' => '1', 'date_souhaitee' => '']];
        }
    }

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

        $this->step = min(3, $this->step + 1);
    }

    public function previousStep(): void
    {
        $this->resetValidation();
        $this->step = max(1, $this->step - 1);
    }

    public function save(): void
    {
        Gate::authorize('update', $this->project);
        $validated = $this->validate();

        $this->project->update([
            'titre' => $validated['titre'],
            'categorie' => $validated['categorie'],
            'description' => $validated['description'],
            'besoin_financier_target' => $validated['besoinFinancierTarget'] ?: null,
            'besoins_competences' => array_values(array_filter(
                $validated['competences'],
                fn (array $competence): bool => trim($competence['role']) !== '',
            )),
            'besoins_materiels' => array_values(array_filter(array_map(
                fn (array $materiel): ?array => trim($materiel['nom']) === '' ? null : [
                    'label' => trim($materiel['nom']),
                    'quantite' => $materiel['quantite'] !== '' ? (int) $materiel['quantite'] : 1,
                    'date_souhaitee' => $materiel['date_souhaitee'] ?: null,
                ],
                $validated['materiels'],
            ))),
        ]);

        session()->flash('project-message', 'Les informations du projet ont été mises à jour.');
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
            'materiels.*.nom' => ['nullable', 'string', 'max:150'],
            'materiels.*.quantite' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'materiels.*.date_souhaitee' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function render(): View
    {
        return view('livewire.edit-project')->layout('layouts.app');
    }

    /** @param array<int, mixed> $items */
    private function normaliseCompetences(array $items): array
    {
        return array_values(array_filter(array_map(function (mixed $item): ?array {
            if (is_string($item)) {
                return ['role' => $item, 'niveau' => ''];
            }

            if (! is_array($item)) {
                return null;
            }

            return [
                'role' => (string) ($item['role'] ?? $item['label'] ?? $item['name'] ?? ''),
                'niveau' => (string) ($item['niveau'] ?? ''),
            ];
        }, $items), fn (?array $item): bool => $item !== null));
    }

    /** @param array<int, mixed> $items */
    private function normaliseMateriels(array $items): array
    {
        return array_values(array_filter(array_map(function (mixed $item): ?array {
            if (is_string($item)) {
                return ['nom' => $item, 'quantite' => '1', 'date_souhaitee' => ''];
            }

            if (! is_array($item)) {
                return null;
            }

            return [
                'nom' => (string) ($item['label'] ?? $item['name'] ?? ''),
                'quantite' => (string) ($item['quantite'] ?? 1),
                'date_souhaitee' => (string) ($item['date_souhaitee'] ?? ''),
            ];
        }, $items), fn (?array $item): bool => $item !== null));
    }
}
