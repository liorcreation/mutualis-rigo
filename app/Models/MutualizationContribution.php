<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutualizationContribution extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'user_id',
        'type_apport',
        'montant',
        'description_apport',
        'competence_nom',
        'competence_niveau',
        'materiel_nom',
        'materiel_quantite_disponible',
        'materiel_unite',
        'materiel_etat',
        'materiel_localisation',
        'materiel_disponible_du',
        'materiel_disponible_au',
        'statut',
        'commentaire_validation',
        'validated_by',
        'validated_at',
        'decision_reason',
    ];

    protected $casts = [
        'type_apport' => ContributionType::class,
        'statut' => ContributionStatus::class,
        'montant' => 'decimal:2',
        'materiel_quantite_disponible' => 'integer',
        'materiel_disponible_du' => 'date',
        'materiel_disponible_au' => 'date',
        'validated_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Compatibilité avec les apports matériels créés avant les champs
     * structurés : une ancienne contribution représente une unité.
     */
    public function availableMaterialQuantity(): int
    {
        if ($this->materiel_quantite_disponible !== null) {
            return max(1, (int) $this->materiel_quantite_disponible);
        }

        if (is_string($this->description_apport)
            && preg_match('/^\s*(\d+)/', $this->description_apport, $matches) === 1) {
            return max(1, (int) $matches[1]);
        }

        return 1;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'contribution_id');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class, 'contribution_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(MaterialReservation::class, 'contribution_id');
    }
}
