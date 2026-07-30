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

class MutualizationContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'type_apport',
        'montant',
        'description_apport',
        'statut',
        'commentaire_validation',
    ];

    protected $casts = [
        'type_apport' => ContributionType::class,
        'statut' => ContributionStatus::class,
        'montant' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'contribution_id');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class, 'contribution_id');
    }
}
