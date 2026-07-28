<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // On appelle UNIQUEMENT notre UserSeeder personnalisé
        $this->call([
            UserSeeder::class,
        ]);
    }
}