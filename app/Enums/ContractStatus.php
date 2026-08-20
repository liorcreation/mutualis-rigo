<?php

declare(strict_types=1);

namespace App\Enums;

enum ContractStatus: string
{
    case DRAFT = 'draft';
    case PENDING_SIGNATURE = 'pending_signature';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Brouillon',
            self::PENDING_SIGNATURE => 'En attente de signature',
            self::ACTIVE => 'Actif',
            self::COMPLETED => 'Terminé',
            self::CANCELLED => 'Annulé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'slate',
            self::PENDING_SIGNATURE => 'amber',
            self::ACTIVE => 'emerald',
            self::COMPLETED => 'indigo',
            self::CANCELLED => 'rose',
        };
    }
}
