<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Models\User;

class MessagePolicy
{
    /**
     * Un porteur de projet peut échanger avec n'importe lequel de ses
     * contributeurs (éventuellement filtré sur une contribution précise) ;
     * un contributeur ne peut échanger qu'avec le porteur du projet auquel
     * il a apporté quelque chose. Personne ne peut se contacter soi-même.
     */
    public function view(User $user, Project $project, User $participant, ?int $contributionId = null): bool
    {
        if ($participant->id === $user->id) {
            return false;
        }

        if ($project->user_id === $user->id) {
            return $contributionId === null || MutualizationContribution::query()
                ->whereKey($contributionId)
                ->where('project_id', $project->id)
                ->where('user_id', $participant->id)
                ->exists();
        }

        return $participant->id === $project->user_id
            && MutualizationContribution::query()
                ->where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->exists();
    }
}
