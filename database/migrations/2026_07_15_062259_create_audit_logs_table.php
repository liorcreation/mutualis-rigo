<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // ex: CREATION OU MODIFICATION DE PROJET
            $table->text('donnees_auditees'); // Stocke le payload JSON des données
            $table->string('hash_parent'); // Le hash SHA-256 du bloc précédent
            $table->string('hash_actuel'); // Le hash SHA-256 calculé pour ce bloc
            $table->timestamp('enregistre_le'); // Date et heure précises de signature
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
