<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;

class ContributionPolicy
{
    /**
     * Autorisation générique pour proposer une contribution.
     */
    public function create(User $user, Project $project): bool
    {
        return $this->isVerified($user) || $this->hasAuthorizedRole($user);
    }

    /**
     * Les apports financiers sont réservés aux profils vérifiés ou à Finance.
     */
    public function createFinancial(User $user, Project $project): bool
    {
        return $this->isVerified($user) || match ($user->role) {
            UserRole::RESPONSABLE_FINANCIER,
            UserRole::TOP_MANAGEMENT,
            UserRole::ADMIN_SYSTEME => true,
            default => false,
        };
    }

    /**
     * Le matériel lourd est réservé aux profils vérifiés ou aux rôles opérationnels.
     */
    public function createMaterial(User $user, Project $project): bool
    {
        return $this->isVerified($user) || match ($user->role) {
            UserRole::CHEF_PROJET,
            UserRole::TOP_MANAGEMENT,
            UserRole::ADMIN_SYSTEME => true,
            default => false,
        };
    }

    private function isVerified(User $user): bool
    {
        return $user->profile?->is_verified === true;
    }

    private function hasAuthorizedRole(User $user): bool
    {
        return match ($user->role) {
            UserRole::RESPONSABLE_RH,
            UserRole::RESPONSABLE_FINANCIER,
            UserRole::CHEF_PROJET,
            UserRole::TOP_MANAGEMENT,
            UserRole::ADMIN_SYSTEME => true,
            default => false,
        };
    }
}
