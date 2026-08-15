<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Création d'un acteur "Personne Morale" (ex: Une entreprise partenaire)
        User::create([
            'name' => 'Lior Creation Sarl',
            'email' => 'contact@liorcreation.com',
            'password' => Hash::make('password123'),
            'role' => 'personne_morale',
            'telephone' => '+226 70 00 00 00',
            'nom_entreprise' => 'LIOR CREATION',
            'ifu_entreprise' => '00123456X',
        ]);

        // 2. Création d'un acteur "Personne Physique" (ex: Un consultant externe indépendant)
        User::create([
            'name' => 'Steve Diendere',
            'email' => 'steve@example.com',
            'password' => Hash::make('password123'),
            'role' => 'personne_physique',
            'telephone' => '+226 76 00 00 00',
            'cnib_passport' => 'B1234567',
        ]);

        // 3. Création d'un employé interne "Chef de Projet" chez Rigo
        User::create([
            'name' => 'Collaborateur Rigo Tech',
            'email' => 'chef@rigo.com',
            'password' => Hash::make('password123'),
            'role' => 'chef_projet',
            'matricule' => 'RIGO-2026-009',
            'departement' => 'Technique',
        ]);
    }
}
