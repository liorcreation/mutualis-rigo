<?php

declare(strict_types=1);

namespace App\Enums;

enum ReservationStatus: string
{
    case EN_ATTENTE = 'en_attente';
    case VALIDEE = 'validee';
    case REFUSEE = 'refusee';
    case ANNULEE = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::VALIDEE => 'Validée',
            self::REFUSEE => 'Refusée',
            self::ANNULEE => 'Annulée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'amber',
            self::VALIDEE => 'emerald',
            self::REFUSEE => 'rose',
            self::ANNULEE => 'slate',
        };
    }
}
