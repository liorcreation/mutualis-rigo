<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case BROUILLON = 'brouillon';
    case EN_ETUDE = 'en_etude';
    case EN_COURS_DE_MUTUALISATION = 'en_cours_de_mutualisation';
    case CLOTURE = 'cloture';
}
