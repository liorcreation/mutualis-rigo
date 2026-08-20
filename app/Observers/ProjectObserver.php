<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Project;

/**
 * Scelle les évènements significatifs d'un projet dans le registre d'audit
 * chaîné cryptographiquement : chaque bloc dépend du hash SHA-256 du précédent.
 */
class ProjectObserver
{
    private const GENESIS_HASH = 'INITIAL_HASH_MUTUALIS_RIGO_2026';

    public function created(Project $project): void
    {
        $this->seal('Création de projet', $project);
    }

    public function updated(Project $project): void
    {
        if (! $project->wasChanged('statut')) {
            return;
        }

        $this->seal('Changement de statut du projet', $project);
    }

    private function seal(string $action, Project $project): void
    {
        $hashParent = AuditLog::query()->latest('id')->value('hash_actuel') ?? self::GENESIS_HASH;

        $payload = json_encode([
            'projet_id' => $project->id,
            'titre' => $project->titre,
            'statut' => $project->statut?->value,
            'user_id' => $project->user_id,
        ], JSON_THROW_ON_ERROR);

        AuditLog::create([
            'action' => $action,
            'donnees_auditees' => $payload,
            'hash_parent' => $hashParent,
            'hash_actuel' => hash('sha256', $hashParent.$payload),
            'enregistre_le' => now(),
        ]);
    }
}
