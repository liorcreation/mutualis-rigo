<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cette table va contenir TOUS nos utilisateurs (Héritage UML traduit en STI - Single Table Inheritance)
        Schema::create('users', function (Blueprint $table) {
            // 1. Informations d'identification de base
            $table->id();
            $table->string('name'); // Nom complet ou Raison sociale de l'entreprise
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // 2. Le Rôle (Détermine quel acteur de ton diagramme est connecté)
            // Voir App\Enums\UserRole pour la liste canonique des valeurs.
            $table->string('role')->default('personne_physique');

            // 3. Champs spécifiques aux Utilisateurs Externes (Porteurs de projets)
            $table->string('telephone')->nullable();
            $table->string('nom_entreprise')->nullable(); // Rempli uniquement si Personne Morale
            $table->string('ifu_entreprise')->nullable();  // Identifiant Fiscal Unique ( Burkina Faso - Personne Morale )
            $table->string('cnib_passport')->nullable();  // Pièce d'identité ( si Personne Physique )

            // 4. Champs spécifiques aux Employés Internes de Rigo
            $table->string('matricule')->nullable(); // Matricule de l'employé
            $table->string('departement')->nullable(); // Ex: "Technique", "Finance", "RH"

            $table->rememberToken();
            $table->timestamps();
        });

        // Tables techniques par défaut de Laravel (ne pas toucher)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
