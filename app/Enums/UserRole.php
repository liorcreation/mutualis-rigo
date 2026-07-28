<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Rôles disponibles sur la plateforme de mutualisation RIGO.
 */
enum UserRole: string
{
    case PERSONNE_PHYSIQUE = 'personne_physique';
    case PERSONNE_MORALE = 'personne_morale';
    case CHEF_PROJET = 'chef_projet';
    case RESPONSABLE_RH = 'responsable_rh';
    case RESPONSABLE_FINANCIER = 'responsable_financier';
    case TOP_MANAGEMENT = 'top_management';
    case COLLABORATEUR = 'collaborateur';
    case ADMIN_SYSTEME = 'admin_systeme';

    /**
     * Indique si le rôle appartient aux utilisateurs externes.
     */
    public function isExternal(): bool
    {
        return match ($this) {
            self::PERSONNE_PHYSIQUE, self::PERSONNE_MORALE => true,
            default => false,
        };
    }

    /**
     * Indique si le rôle appartient au personnel interne.
     */
    public function isInternal(): bool
    {
        return match ($this) {
            self::CHEF_PROJET,
            self::RESPONSABLE_RH,
            self::RESPONSABLE_FINANCIER,
            self::TOP_MANAGEMENT,
            self::COLLABORATEUR => true,
            default => false,
        };
    }
}
