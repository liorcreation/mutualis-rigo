<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Collection;

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

    public function label(): string
    {
        return match ($this) {
            self::BROUILLON => 'Brouillon',
            self::EN_ETUDE => 'En étude',
            self::EN_COURS_DE_MUTUALISATION => 'En cours de mutualisation',
            self::CLOTURE => 'Clôturé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BROUILLON => 'slate',
            self::EN_ETUDE => 'amber',
            self::EN_COURS_DE_MUTUALISATION => 'indigo',
            self::CLOTURE => 'emerald',
        };
    }

    /**
     * Empêche les retours arrière et les transitions incohérentes.
     *
     * @return Collection<int, string>
     */
    public function allowedTransitions(): Collection
    {
        return match ($this) {
            self::EN_ETUDE => collect([
                self::EN_COURS_DE_MUTUALISATION->value,
                self::CLOTURE->value,
            ]),
            self::EN_COURS_DE_MUTUALISATION => collect([
                self::CLOTURE->value,
            ]),
            default => collect(),
        };
    }
}
