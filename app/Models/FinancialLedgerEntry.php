<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * Écriture append-only du registre financier de Mutualis.
 */
class FinancialLedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'source_project_id',
        'destination_project_id',
        'entry_type',
        'amount',
        'reference',
        'metadata',
        'hash_parent',
        'hash_actuel',
        'enregistre_le',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'enregistre_le' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new LogicException('Le registre financier est append-only.');
        });

        static::deleting(function (): void {
            throw new LogicException('Une écriture du registre financier ne peut pas être supprimée.');
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sourceProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'source_project_id');
    }

    public function destinationProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'destination_project_id');
    }
}
