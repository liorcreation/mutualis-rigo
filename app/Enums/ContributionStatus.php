<?php

declare(strict_types=1);

namespace App\Enums;

enum ContributionStatus: string
{
    case EN_ATTENTE = 'en_attente';
    case VALIDE = 'valide';
    case REFUSE = 'refuse';
}
