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
                'password' => Hash::make('123456'),
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
                'password' => Hash::make('123456'),
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
                'password' => Hash::make('123456'),
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
                'password' => Hash::make('123456'),
                'role' => 'admin_systeme',
                'matricule' => 'RIGO-2026-001',
                'departement' => 'Direction Générale',
            ]
        );

        // 5. Responsable RH (valide les apports de type "compétence")
        User::updateOrCreate(
            ['email' => 'rh@rigo.com'],
            [
                'name' => 'Responsable RH Rigo',
                'password' => Hash::make('123456'),
                'role' => 'responsable_rh',
                'matricule' => 'RIGO-2026-002',
                'departement' => 'Ressources Humaines',
            ]
        );

        // 6. Responsable Financier (valide les apports de type "financier")
        User::updateOrCreate(
            ['email' => 'financier@rigo.com'],
            [
                'name' => 'Responsable Financier Rigo',
                'password' => Hash::make('123456'),
                'role' => 'responsable_financier',
                'matricule' => 'RIGO-2026-003',
                'departement' => 'Finance',
            ]
        );

        // 7. Top Management (accès complet au back-office, hors administration système)
        User::updateOrCreate(
            ['email' => 'direction@rigo.com'],
            [
                'name' => 'Top Management Rigo',
                'password' => Hash::make('123456'),
                'role' => 'top_management',
                'matricule' => 'RIGO-2026-004',
                'departement' => 'Direction Générale',
            ]
        );
    }
}
