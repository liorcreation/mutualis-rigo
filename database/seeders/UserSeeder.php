<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * On utilise updateOrCreate (clé = email) plutôt que create() : si un
     * compte existe déjà en base avec un mot de passe ou un rôle obsolète,
     * relancer le seeder le réaligne automatiquement au lieu d'échouer sur
     * la contrainte d'unicité de l'email.
     */
    public function run(): void
    {
        // 1. Acteur "Personne Morale" (ex: une entreprise partenaire externe)
        User::updateOrCreate(
            ['email' => 'contact@liorcreation.com'],
            [
                'name' => 'Lior Creation Sarl',
                'password' => Hash::make('password123'),
                'role' => 'personne_morale',
                'telephone' => '+226 70 00 00 00',
                'nom_entreprise' => 'LIOR CREATION',
                'ifu_entreprise' => '00123456X',
            ]
        );

        // 2. Acteur "Personne Physique" (ex: un consultant externe indépendant)
        User::updateOrCreate(
            ['email' => 'steve@example.com'],
            [
                'name' => 'Steve Diendere',
                'password' => Hash::make('password123'),
                'role' => 'personne_physique',
                'telephone' => '+226 76 00 00 00',
                'cnib_passport' => 'B1234567',
            ]
        );

        // 3. Employé interne "Chef de Projet" chez Rigo
        User::updateOrCreate(
            ['email' => 'chef@rigo.com'],
            [
                'name' => 'Collaborateur Rigo Tech',
                'password' => Hash::make('password123'),
                'role' => 'chef_projet',
                'matricule' => 'RIGO-2026-009',
                'departement' => 'Technique',
            ]
        );

        // 4. Administrateur système (accès complet au back-office)
        User::updateOrCreate(
            ['email' => 'admin@rigo.com'],
            [
                'name' => 'Administrateur Rigo',
                'password' => Hash::make('password123'),
                'role' => 'admin_systeme',
                'matricule' => 'RIGO-2026-001',
                'departement' => 'Direction Générale',
            ]
        );
    }
}
