<?php

namespace App\Observers;

use App\Models\Projet;
use App\Models\TraceAudit;
use Illuminate\Support\Facades\Auth;

class ProjetObserver
{
    /**
     * Cette méthode se déclenche automatiquement JUSTE APRÈS 
     * qu'un projet a été créé avec succès en base de données.
     */
    public function created(Projet $projet): void
    {
        // 1. Chaînage cryptographique : On récupère le hash de la toute dernière trace d'audit
        $dernierAudit = TraceAudit::latest('id')->first();
        
        // Si c'est le tout premier audit du système, on définit un hash initial (bloc genèse)
        $hashPrecedent = $dernierAudit ? $dernierAudit->hash : 'INITIAL_HASH_MUTUALIS_RIGO_2026';

        // 2. Préparation des données du projet pour le calcul de l'empreinte
        // Nous formatons les données clés en JSON pour garantir l'uniformité du hachage
        $donneesAHash = json_encode([
            'action' => 'Création de projet',
            'table' => 'projets',
            'enregistrement_id' => $projet->id,
            'user_id' => $projet->user_id,
            'titre' => $projet->titre,
            'budget' => $projet->budget,
            'hash_precedent' => $hashPrecedent, // Intégration du hash précédent pour le chaînage
        ]);

        // 3. Calcul du SHA-256 (Correspond à l'étape calculerSHA256() de ton diagramme de séquence)
        $hashActuel = hash('sha256', $donneesAHash);

        // 4. Enregistrement de la trace d'audit (Correspond à l'étape creerTraceLog())
        TraceAudit::create([
            'user_id' => $projet->user_id,
            'action' => 'Création de projet',
            'table_concernee' => 'projets',
            'enregistrement_id' => $projet->id,
            'hash_precedent' => $hashPrecedent,
            'hash' => $hashActuel,
        ]);
    }
}