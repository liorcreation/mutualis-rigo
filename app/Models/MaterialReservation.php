<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialReservation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'contribution_id',
        'requested_by',
        'date_debut',
        'date_fin',
        'quantite',
        'statut',
        'commentaire',
        'commentaire_validation',
        'validated_by',
        'validated_at',
        'decision_reason',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'quantite' => 'integer',
        'statut' => ReservationStatus::class,
        'validated_at' => 'datetime',
    ];

    public function contribution(): BelongsTo
    {
        return $this->belongsTo(MutualizationContribution::class, 'contribution_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Demandes encore susceptibles de bloquer le matériel (en attente ou validées).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('statut', [
            ReservationStatus::EN_ATTENTE->value,
            ReservationStatus::VALIDEE->value,
        ]);
    }

    /**
     * Chevauchement de périodes : deux créneaux se recoupent dès que
     * le début de l'un précède la fin de l'autre et inversement.
     */
    public function scopeOverlapping(Builder $query, string $start, string $end): Builder
    {
        return $query->where('date_debut', '<=', $end)->where('date_fin', '>=', $start);
    }

    /**
     * Vérifie qu'aucune demande active sur ce matériel ne chevauche la période souhaitée.
     */
    public static function isAvailable(
        int $contributionId,
        string $start,
        string $end,
        int $requestedQuantity = 1,
        ?int $excludeId = null,
    ): bool {
        $contribution = MutualizationContribution::query()->find($contributionId);

        if ($contribution === null || $requestedQuantity < 1) {
            return false;
        }

        $reservedQuantity = (int) static::query()
            ->where('contribution_id', $contributionId)
            ->active()
            ->overlapping($start, $end)
            ->when($excludeId !== null, fn (Builder $query): Builder => $query->where('id', '!=', $excludeId))
            ->sum('quantite');

        return $reservedQuantity + $requestedQuantity <= $contribution->availableMaterialQuantity();
    }

    public static function remainingQuantity(int $contributionId, string $start, string $end, ?int $excludeId = null): int
    {
        $contribution = MutualizationContribution::query()->find($contributionId);

        if ($contribution === null) {
            return 0;
        }

        $reservedQuantity = (int) static::query()
            ->where('contribution_id', $contributionId)
            ->active()
            ->overlapping($start, $end)
            ->when($excludeId !== null, fn (Builder $query): Builder => $query->where('id', '!=', $excludeId))
            ->sum('quantite');

        return max(0, $contribution->availableMaterialQuantity() - $reservedQuantity);
    }
}
