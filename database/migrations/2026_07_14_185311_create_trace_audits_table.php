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
        Schema::create('trace_audits', function (Blueprint $table) {
            $table->id(); // Identifiant de la trace
            
            // L'utilisateur (l'acteur) qui a effectué l'action. 
            // Si l'utilisateur est supprimé, on garde la trace mais ce champ passe à NULL (nullOnDelete)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('action'); // Ex: "Création de projet", "Modification de statut"
            $table->string('table_concernee'); // Ex: "projets"
            $table->unsignedBigInteger('enregistrement_id'); // L'ID du projet concerné
            
            // Sécurité Cryptographique (SHA-256)
            $table->string('hash_precedent')->nullable(); // Signature du bloc d'audit précédent (pour le chaînage)
            $table->string('hash'); // Signature SHA-256 de la ligne actuelle combinée au hash_precedent

            $table->timestamps(); // Permet de dater précisément quand l'action a eu lieu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trace_audits');
    }
};