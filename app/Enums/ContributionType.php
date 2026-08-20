<?php

declare(strict_types=1);

namespace App\Enums;

enum ContributionType: string
{
    case FINANCIER = 'financier';
    case COMPETENCE = 'competence';
    case MATERIEL = 'materiel';

    public function label(): string
    {
        return match ($this) {
            self::FINANCIER => 'Financier',
            self::COMPETENCE => 'Compétence',
            self::MATERIEL => 'Matériel',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::FINANCIER => 'emerald',
            self::COMPETENCE => 'fuchsia',
            self::MATERIEL => 'amber',
        };
    }

    /**
     * Types d'apport que ce rôle est habilité à valider : finance et RH ne
     * voient que leur périmètre métier, top management et administration
     * voient tout.
     *
     * @return list<string>
     */
    public static function allowedFor(UserRole $role): array
    {
        return match ($role) {
            UserRole::ADMIN_SYSTEME, UserRole::TOP_MANAGEMENT => array_column(self::cases(), 'value'),
            UserRole::RESPONSABLE_FINANCIER => [self::FINANCIER->value],
            UserRole::RESPONSABLE_RH => [self::COMPETENCE->value],
            UserRole::CHEF_PROJET, UserRole::COLLABORATEUR => [self::MATERIEL->value],
            default => [],
        };
    }
}
