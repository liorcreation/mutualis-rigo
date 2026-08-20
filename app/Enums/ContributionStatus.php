<?php

declare(strict_types=1);

namespace App\Enums;

enum ContributionStatus: string
{
    case EN_ATTENTE = 'en_attente';
    case VALIDE = 'valide';
    case REFUSE = 'refuse';

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::VALIDE => 'Validé',
            self::REFUSE => 'Refusé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'amber',
            self::VALIDE => 'emerald',
            self::REFUSE => 'rose',
        };
    }
}
