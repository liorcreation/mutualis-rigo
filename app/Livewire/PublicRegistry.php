<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\User;
use App\Models\Projet;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class PublicRegistry extends Component
{
    // Propriétés du formulaire (harmonisées avec la vue Blade)
    public string $titre = '';
    public string $rh = '';
    public $finance = '';
    public string $materiel = '';

    // Simulation d'acteurs
    public array $actors = [
        [
            'id' => 1,
            'nom' => 'Steve Diendere',
            'email' => 'steve@example.com',
            'role' => 'PERSONNE PHYSIQUE'
        ],
        [
            'id' => 2,
            'nom' => 'Use Creation Sarl',
            'email' => 'contact@usecreation.bf',
            'role' => 'PERSONNE MORALE'
        ]
    ];
    
    public int $selectedActorId = 1;

    // Règles de validation assouplies
    protected array $rules = [
        'titre'    => 'required|string|max:255',
        'rh'       => 'nullable|string',
        'finance'  => 'nullable|numeric|min:0',
        'materiel' => 'nullable|string',
    ];

    #[Computed]
    public function selectedActor()
    {
        return collect($this->actors)->firstWhere('id', $this->selectedActorId) 
            ?? $this->actors[0];
    }

    public function getSelectedActorProperty()
    {
        return $this->selectedActor();
    }

    public function submit(): void
    {
        // 1. Validation
        $this->validate();

        $acteur = $this->selectedActor();

        // 2. Enregistrement du projet
        $projet = Projet::create([
            'titre'           => $this->titre,
            'description'     => "Besoin RH : " . ($this->rh ?: 'N/A'),
            'budget'          => (float) ($this->finance ?? 0),
            'materiel_requis' => $this->materiel ?: 'N/A',
            'statut'          => 'en_etude',
            'user_id'         => Auth::id() ?? 1,
        ]);

        // 3. Chaînage SHA-256
        $dernierBloc = AuditLog::latest('id')->first();

        $hashParent = $dernierBloc 
            ? $dernierBloc->hash_actuel 
            : 'INITIAL_HASH_MUTUALIS_ASSO_MIN_YODE';

        $donneesAuditees = json_encode([
            'projet_id' => $projet->id,
            'action'    => 'CREATION DE PROJET',
            'auteur'    => $acteur['nom'] . ' (' . $acteur['email'] . ')',
            'titre'     => $this->titre,
            'rh'        => $this->rh,
            'finance'   => $this->finance,
            'materiel'  => $this->materiel,
        ], JSON_UNESCAPED_UNICODE);

        $timestamp = now()->toDateTimeString();
        $hashActuel = hash('sha256', $hashParent . $donneesAuditees . $timestamp);

        AuditLog::create([
            'action'           => 'CREATION OU MODIFICATION DE PROJET',
            'donnees_auditees' => $donneesAuditees,
            'hash_parent'      => $hashParent,
            'hash_actuel'      => $hashActuel,
            'enregistre_le'    => $timestamp,
        ]);

        // 4. Réinitialisation des champs du formulaire
        $this->reset(['titre', 'rh', 'finance', 'materiel']);

        // Message Flash (clé 'message' harmonisée avec le Blade)
        session()->flash('message', 'Votre demande de mutualisation a été signée par SHA-256 et scellée avec succès !');
    }

    public function publier(): void
    {
        $this->submit();
    }

    public function render()
    {
        return view('livewire.public-registry', [
            'users'     => User::all(),
            'projets'   => Projet::latest()->get(),
            'audits'    => AuditLog::latest()->get(),
            'auditLogs' => AuditLog::latest()->get(),
        ]);
    }
}
