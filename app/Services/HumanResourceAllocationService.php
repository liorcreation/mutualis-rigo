<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectUserAssignment;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Gère la capacité disponible d'un collaborateur sur une période.
 *
 * Une personne peut contribuer à plusieurs projets, mais la somme des taux
 * qui se chevauchent ne doit jamais dépasser 100 %.
 */
class HumanResourceAllocationService
{
    public function assign(
        Project $project,
        User $user,
        int $percentageLoad,
        CarbonInterface $startDate,
        ?CarbonInterface $endDate = null,
        ?string $role = null,
    ): ProjectUserAssignment {
        $this->validateDates($startDate, $endDate);
        $this->validatePercentage($percentageLoad);

        return DB::transaction(function () use (
            $project,
            $user,
            $percentageLoad,
            $startDate,
            $endDate,
            $role,
        ): ProjectUserAssignment {
            $assignments = $this->overlappingAssignmentsQuery(
                $user,
                $startDate,
                $endDate,
            )->lockForUpdate()->get();

            $total = (int) $assignments->sum('percentage_load') + $percentageLoad;

            if ($total > 100) {
                throw ValidationException::withMessages([
                    'percentage_load' => sprintf(
                        'Cette affectation porterait la charge de %s à %d %%. La limite autorisée est de 100 %%.',
                        $user->name,
                        $total,
                    ),
                ]);
            }

            return ProjectUserAssignment::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'role_recherche' => $role ? trim($role) : null,
                'percentage_load' => $percentageLoad,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate?->toDateString(),
            ]);
        });
    }

    public function totalLoad(User $user, CarbonInterface $date): int
    {
        return (int) $user->projectAssignments()
            ->whereDate('start_date', '<=', $date->toDateString())
            ->where(function (Builder $query) use ($date): void {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date->toDateString());
            })
            ->sum('percentage_load');
    }

    /**
     * Retourne les affectations actives à une date donnée.
     */
    public function assignmentsForDate(User $user, CarbonInterface $date): mixed
    {
        return $user->projectAssignments()
            ->with('project')
            ->whereDate('start_date', '<=', $date->toDateString())
            ->where(function (Builder $query) use ($date): void {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date->toDateString());
            })
            ->latest('start_date')
            ->get();
    }

    public function remove(ProjectUserAssignment $assignment): void
    {
        DB::transaction(fn (): ?bool => $assignment->delete());
    }

    private function overlappingAssignmentsQuery(
        User $user,
        CarbonInterface $startDate,
        ?CarbonInterface $endDate,
    ): Builder {
        $lastDate = $endDate?->toDateString() ?? '9999-12-31';

        return ProjectUserAssignment::query()
            ->where('user_id', $user->id)
            ->whereDate('start_date', '<=', $lastDate)
            ->where(function (Builder $query) use ($startDate): void {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $startDate->toDateString());
            });
    }

    private function validateDates(CarbonInterface $startDate, ?CarbonInterface $endDate): void
    {
        if ($endDate !== null && $endDate->isBefore($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => 'La date de fin doit être postérieure ou égale à la date de début.',
            ]);
        }
    }

    private function validatePercentage(int $percentageLoad): void
    {
        if ($percentageLoad < 1 || $percentageLoad > 100) {
            throw ValidationException::withMessages([
                'percentage_load' => 'Le taux d’affectation doit être compris entre 1 % et 100 %.',
            ]);
        }
    }
}
