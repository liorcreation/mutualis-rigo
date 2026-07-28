<?php

declare(strict_types=1);

namespace App\Enums;

enum ContributionType: string
{
    case FINANCIER = 'financier';
    case COMPETENCE = 'competence';
    case MATERIEL = 'materiel';
}
