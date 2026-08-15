<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\MaterialReservation;
use App\Models\MutualizationContribution;
use App\Models\User;

class MaterialReservationPolicy
{
    /**
     * Qui peut demander à réserver un matériel mutualisé.
     * On ne peut pas réserver son propre apport.
     */
    public function create(User $user, MutualizationContribution $contribution): bool
    {
        if ($contribution->user_id === $user->id) {
            return false;
        }

        return $this->isVerified($user) || $this->hasAuthorizedRole($user);
    }

    /**
     * Seul le contributeur propriétaire du matériel valide ou refuse les demandes le concernant.
     */
    public function validate(User $user, MaterialReservation $reservation): bool
    {
        return $reservation->contribution->user_id === $user->id;
    }

    /**
     * Le demandeur peut annuler sa propre demande tant qu'elle n'a pas été tranchée.
     */
    public function cancel(User $user, MaterialReservation $reservation): bool
    {
        return $reservation->requested_by === $user->id;
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
            UserRole::COLLABORATEUR,
            UserRole::TOP_MANAGEMENT,
            UserRole::ADMIN_SYSTEME => true,
            default => false,
        };
    }
}
