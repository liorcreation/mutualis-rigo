<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Models\MutualizationContribution;
use App\Models\Project;
use Illuminate\Support\Collection;

/**
 * Calcule l'état d'avancement d'un projet à partir de ses apports validés.
 */
class ProjectMutualizationService
{
    /**
     * Retourne les progressions financière et humaine, en pourcentage.
     *
     * @return array{financial: float, human: float}
     */
    public function progress(Project $project): array
    {
        return [
            'financial' => $this->financialProgress($project),
            'human' => $this->humanProgress($project),
        ];
    }

    /**
     * Recalcule le montant financier validé et le persiste sur le projet.
     * La progression humaine reste calculée à la volée depuis les apports.
     *
     * @return array{financial: float, human: float}
     */
    public function recalculate(Project $project): array
    {
        $current = (float) $this->validatedContributions($project)
            ->where('type_apport', ContributionType::FINANCIER)
            ->sum('montant');

        $project->forceFill([
            'besoin_financier_actuel' => $current,
        ])->saveQuietly();

        return $this->progress($project->fresh());
    }

    public function financialProgress(Project $project): float
    {
        $target = (float) $project->besoin_financier_target;

        if ($target <= 0) {
            return 0.0;
        }

        $current = (float) $this->validatedContributions($project)
            ->where('type_apport', ContributionType::FINANCIER)
            ->sum('montant');

        return $this->percentage($current, $target);
    }

    public function humanProgress(Project $project): float
    {
        $requiredSkills = collect($project->besoins_competences ?? [])
            ->map(function (mixed $competence): ?string {
                $role = is_array($competence) ? ($competence['role'] ?? null) : $competence;

                return is_string($role) && trim($role) !== ''
                    ? mb_strtolower(trim($role))
                    : null;
            })
            ->filter()
            ->unique()
            ->values();

        if ($requiredSkills->isEmpty()) {
            return 0.0;
        }

        $provided = $this->validatedContributions($project)
            ->where('type_apport', ContributionType::COMPETENCE)
            ->pluck('description_apport')
            ->filter()
            ->flatMap(fn (string $description): array => preg_split('/[,;]+/', $description) ?: [])
            ->map(fn (string $competence): string => mb_strtolower(trim($competence)))
            ->filter()
            ->unique()
            ->intersect($requiredSkills)
            ->count();

        return $this->percentage((float) $provided, (float) $requiredSkills->count());
    }

    private function percentage(float $current, float $target): float
    {
        return round(min(100.0, max(0.0, ($current / $target) * 100)), 2);
    }

    /**
     * Utilise la relation déjà chargée pour éviter une requête par carte.
     */
    private function validatedContributions(Project $project): Collection
    {
        $contributions = $project->relationLoaded('contributions')
            ? $project->contributions
            : $project->contributions()->get();

        return $contributions->filter(
            fn (MutualizationContribution $contribution): bool => $contribution->statut === ContributionStatus::VALIDE
        );
    }
}
