<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case BROUILLON = 'brouillon';
    case EN_ETUDE = 'en_etude';
    case EN_COURS_DE_MUTUALISATION = 'en_cours_de_mutualisation';
    case CLOTURE = 'cloture';

    /**
     * Statuts affichés dans le catalogue public : un projet doit avoir été
     * validé par le comité de pilotage (Admin\ProjectReview) avant que les
     * visiteurs puissent le découvrir. Les brouillons et les projets encore
     * à l'étude restent réservés à leur porteur et aux équipes internes.
     *
     * @return list<self>
     */
    public static function publiclyVisible(): array
    {
        return [self::EN_COURS_DE_MUTUALISATION, self::CLOTURE];
    }
}
