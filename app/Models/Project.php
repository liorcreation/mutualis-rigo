<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Projet de mutualisation. Le nom de table historique est conservé pour
 * rester compatible avec les écrans existants de la plateforme.
 */
class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'projets';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'titre',
        'description',
        'categorie',
        'statut',
        'besoin_financier_target',
        'besoin_financier_actuel',
        'besoins_competences',
        'besoins_materiels',
        // Colonnes historiques encore utilisées par l'interface actuelle.
        'budget',
        'materiel_requis',
    ];

    protected $casts = [
        'statut' => ProjectStatus::class,
        'besoin_financier_target' => 'decimal:2',
        'besoin_financier_actuel' => 'decimal:2',
        'besoins_competences' => 'array',
        'besoins_materiels' => 'array',
        'budget' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(MutualizationContribution::class, 'project_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ProjectQuestion::class);
    }
}
