<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraceAudit extends Model
{
    use HasFactory;

    // On spécifie le nom exact de la table car Laravel utilise le pluriel par défaut (trace_audits)
    protected $table = 'trace_audits';

    protected $fillable = [
        'user_id',
        'action',
        'table_concernee',
        'enregistrement_id',
        'hash_precedent',
        'hash',
    ];

    /**
     * RELATION UML : Une trace d'audit est liée à l'utilisateur qui a fait l'action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}