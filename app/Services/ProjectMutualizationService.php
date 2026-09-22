<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Models\MutualizationContribution;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Calcule l'état d'avancement d'un projet à partir de ses apports validés.
 */
class ProjectMutualizationService
{
    /** @var array<string, int> */
    private const SKILL_LEVELS = [
        'debutant' => 1,
        'junior' => 1,
        'intermediaire' => 2,
        'confirme' => 3,
        'avance' => 3,
        'senior' => 3,
        'expert' => 4,
    ];

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
        $project->forceFill([
            'besoin_financier_actuel' => app(FinancialPoolService::class)->balance($project),
        ])->saveQuietly();

        return $this->progress($project->fresh());
    }

    public function financialProgress(Project $project): float
    {
        $target = (float) $project->besoin_financier_target;

        if ($target <= 0) {
            return 0.0;
        }

        return $this->percentage(app(FinancialPoolService::class)->balance($project), $target);
    }

    public function humanProgress(Project $project): float
    {
        $requiredSkills = collect($project->besoins_competences ?? [])
            ->map(function (mixed $competence): ?array {
                $role = is_array($competence) ? ($competence['role'] ?? null) : $competence;
                $level = is_array($competence) ? ($competence['niveau'] ?? null) : null;
                $key = is_string($role) ? $this->normalizeSkill($role) : '';

                return $key !== ''
                    ? ['key' => $key, 'required_level' => $this->levelRank($level)]
                    : null;
            })
            ->filter()
            ->unique('key')
            ->values();

        if ($requiredSkills->isEmpty()) {
            return 0.0;
        }

        $providedSkills = collect();

        foreach ($this->validatedContributions($project)->where('type_apport', ContributionType::COMPETENCE) as $contribution) {
            if (is_string($contribution->competence_nom) && trim($contribution->competence_nom) !== '') {
                $providedSkills->push([
                    'key' => $this->normalizeSkill($contribution->competence_nom),
                    'level' => $this->levelRank($contribution->competence_niveau),
                ]);

                continue;
            }

            foreach (preg_split('/[,;]+/', (string) $contribution->description_apport) ?: [] as $legacySkill) {
                $key = $this->normalizeSkill($legacySkill);

                if ($key !== '') {
                    $providedSkills->push(['key' => $key, 'level' => 0]);
                }
            }
        }

        $providedByAssignments = $project->userAssignments()
            ->whereDate('start_date', '<=', today()->toDateString())
            ->where(function ($query): void {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', today()->toDateString());
            })
            ->pluck('role_recherche')
            ->filter()
            ->map(fn (string $role): array => [
                'key' => $this->normalizeSkill($role),
                'level' => 0,
            ])
            ->filter(fn (array $skill): bool => $skill['key'] !== '');

        $providedSkills = $providedSkills->merge($providedByAssignments);
        $provided = $requiredSkills->filter(function (array $required) use ($providedSkills): bool {
            return $providedSkills->contains(function (array $provided) use ($required): bool {
                return $provided['key'] === $required['key']
                    && ($required['required_level'] === 0
                        || $provided['level'] === 0
                        || $provided['level'] >= $required['required_level']);
            });
        })->count();

        return $this->percentage((float) $provided, (float) $requiredSkills->count());
    }

    private function normalizeSkill(mixed $value): string
    {
        return Str::of((string) $value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->value();
    }

    private function levelRank(mixed $level): int
    {
        return self::SKILL_LEVELS[$this->normalizeSkill($level)] ?? 0;
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
