<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id
            && $project->statut !== ProjectStatus::CLOTURE;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->user_id
            && $project->statut !== ProjectStatus::CLOTURE;
    }
}
