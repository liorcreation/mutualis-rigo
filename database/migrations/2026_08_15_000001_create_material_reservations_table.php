<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_reservations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contribution_id')->constrained('mutualization_contributions')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->unsignedInteger('quantite')->default(1);
            $table->string('statut')->default('en_attente');
            $table->text('commentaire')->nullable();
            $table->text('commentaire_validation')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['contribution_id', 'statut']);
            $table->index(['date_debut', 'date_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_reservations');
    }
};
