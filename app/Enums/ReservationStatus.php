<?php

declare(strict_types=1);

namespace App\Enums;

enum ReservationStatus: string
{
    case EN_ATTENTE = 'en_attente';
    case VALIDEE = 'validee';
    case REFUSEE = 'refusee';
    case ANNULEE = 'annulee';
}
