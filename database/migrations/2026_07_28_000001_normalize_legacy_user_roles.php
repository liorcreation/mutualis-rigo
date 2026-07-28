<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remplace les anciennes valeurs de rôle avant l'activation du cast enum.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'admin')
            ->update(['role' => 'admin_systeme']);

        DB::table('users')
            ->where('role', 'user')
            ->update(['role' => 'personne_physique']);
    }

    /**
     * Restaure les deux alias historiques lors d'un retour arrière.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('role', 'admin_systeme')
            ->update(['role' => 'admin']);
    }
};
