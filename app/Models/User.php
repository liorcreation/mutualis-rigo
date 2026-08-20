<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Les attributs qui peuvent être remplis en masse (Mass Assignment).
     * Très important pour la sécurité.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telephone',
        'nom_entreprise',
        'ifu_entreprise',
        'cnib_passport',
        'matricule',
        'departement',
    ];

    /**
     * Les attributs qui doivent être masqués (comme le mot de passe).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs à convertir dans des types natifs.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    /**
     * RELATION UML : Un utilisateur a plusieurs projets (1 à *).
     * En Laravel, cela se traduit par "hasMany".
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Apports de mutualisation proposés par l'utilisateur.
     */
    public function mutualizationContributions(): HasMany
    {
        return $this->hasMany(MutualizationContribution::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function projectQuestions(): HasMany
    {
        return $this->hasMany(ProjectQuestion::class);
    }

    /**
     * Un utilisateur possède au plus un profil métier.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * FONCTIONS UTILES : Pour vérifier facilement les rôles des acteurs dans l'application.
     * C'est très propre pour la sécurité et l'affichage dynamique.
     */
    public function isPersonnePhysique(): bool
    {
        return $this->role === UserRole::PERSONNE_PHYSIQUE;
    }

    public function isPersonneMorale(): bool
    {
        return $this->role === UserRole::PERSONNE_MORALE;
    }

    public function isChefProjet(): bool
    {
        return $this->role === UserRole::CHEF_PROJET;
    }

    public function isCollaborateur(): bool
    {
        return $this->role === UserRole::COLLABORATEUR;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN_SYSTEME;
    }

    public function isResponsableRh(): bool
    {
        return $this->role === UserRole::RESPONSABLE_RH;
    }

    public function isResponsableFinancier(): bool
    {
        return $this->role === UserRole::RESPONSABLE_FINANCIER;
    }

    public function isTopManagement(): bool
    {
        return $this->role === UserRole::TOP_MANAGEMENT;
    }
}
