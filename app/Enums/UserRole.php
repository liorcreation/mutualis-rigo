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

    /**
     * Rôles finance/direction habilités à voir les données financières
     * transverses (paiements, contrats de tous les porteurs).
     */
    public function isFinanceOrManagement(): bool
    {
        return match ($this) {
            self::RESPONSABLE_FINANCIER, self::TOP_MANAGEMENT, self::ADMIN_SYSTEME => true,
            default => false,
        };
    }

    /**
     * Rôles habilités à accéder au back-office (validation des apports,
     * historique de sécurité). Sur-ensemble de isFinanceOrManagement().
     */
    public function canAccessBackOffice(): bool
    {
        return match ($this) {
            self::ADMIN_SYSTEME,
            self::TOP_MANAGEMENT,
            self::RESPONSABLE_FINANCIER,
            self::RESPONSABLE_RH,
            self::CHEF_PROJET => true,
            default => false,
        };
    }

    /**
     * Rôles habilités à faire transiter le statut d'un projet (comité de
     * pilotage). Sous-ensemble distinct de canAccessBackOffice() : le
     * financier et le RH n'y ont pas accès.
     */
    public function canReviewProjects(): bool
    {
        return match ($this) {
            self::ADMIN_SYSTEME, self::TOP_MANAGEMENT, self::CHEF_PROJET => true,
            default => false,
        };
    }
}
