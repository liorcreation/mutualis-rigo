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
        Schema::create('projets', function (Blueprint $table) {
            $table->id(); // Identifiant unique du projet
            $table->string('titre'); // Titre du projet de mutualisation
            $table->text('description'); // Description détaillée des besoins/objectifs
            $table->decimal('budget', 15, 2)->nullable(); // Colonne historique, remplacée par les besoins financiers.
            
            // Le statut du projet pour le cycle de validation (UML : en attente, validé, rejeté)
            $table->string('statut')->default('brouillon');
            
            // Clé étrangère : Lie le projet à l'acteur (User) qui l'a créé.
            // Si l'utilisateur est supprimé, ses projets associés le sont aussi (cascadeOnDelete)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps(); // create_at et updated_at (gérés automatiquement)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
