<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContractStatus;
use App\Support\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'project_id',
        'user_id',
        'contribution_id',
        'status',
        'amount',
        'currency',
        'terms_accepted_at',
        'signature_ip',
        'document_hash',
        'pdf_path',
        'signed_at',
        'cancelled_at',
        'metadata',
    ];

    protected $casts = [
        'status' => ContractStatus::class,
        'amount' => 'decimal:2',
        'terms_accepted_at' => 'datetime',
        'signed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Contract $contract): void {
            if ($contract->contract_number === null) {
                $contract->contract_number = ReferenceNumberGenerator::generate('contracts', 'contract_number', 'CTR');
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contribution(): BelongsTo
    {
        return $this->belongsTo(MutualizationContribution::class, 'contribution_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}
