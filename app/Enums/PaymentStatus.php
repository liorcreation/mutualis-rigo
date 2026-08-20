<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESSFUL = 'successful';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::SUCCESSFUL => 'Réussi',
            self::FAILED => 'Échoué',
            self::REFUNDED => 'Remboursé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'amber',
            self::SUCCESSFUL => 'emerald',
            self::FAILED => 'rose',
            self::REFUNDED => 'slate',
        };
    }
}
