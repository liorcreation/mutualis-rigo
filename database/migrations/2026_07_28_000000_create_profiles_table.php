<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée les profils métier liés aux utilisateurs.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Données d'une personne physique.
            $table->string('prenom')->nullable();
            $table->string('nom')->nullable();
            $table->string('titre_professionnel')->nullable();
            $table->json('competences')->nullable();
            $table->text('biographie')->nullable();

            // Données d'une personne morale.
            $table->string('nom_entreprise')->nullable();
            $table->string('rne_siret')->nullable();
            $table->string('secteur_activite')->nullable();
            $table->string('representant_legal')->nullable();
            $table->string('site_web')->nullable();

            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Supprime la table des profils.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
