<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Informations métier complémentaires d'un utilisateur.
 *
 * Les colonnes sont nullable car un profil représente soit une personne
 * physique, soit une personne morale, selon le rôle porté par l'utilisateur.
 */
class Profile extends Model
{
    use HasFactory;

    /**
     * Attributs autorisés en assignation de masse.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'prenom',
        'nom',
        'titre_professionnel',
        'competences',
        'biographie',
        'nom_entreprise',
        'rne_siret',
        'secteur_activite',
        'representant_legal',
        'site_web',
        'is_verified',
    ];

    /**
     * Conversions natives des attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'competences' => 'array',
        'is_verified' => 'boolean',
    ];

    /**
     * Un profil appartient à un seul utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
