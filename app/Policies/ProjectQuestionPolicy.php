<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectQuestionPolicy
{
    /**
     * Seul le porteur du projet peut répondre aux questions posées sur sa FAQ.
     */
    public function answer(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }
}
