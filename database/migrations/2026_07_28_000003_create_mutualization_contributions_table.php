<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutualization_contributions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained('projets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type_apport');
            $table->decimal('montant', 15, 2)->nullable();
            $table->text('description_apport')->nullable();
            $table->string('statut')->default('en_attente');
            $table->timestamps();

            $table->index(['project_id', 'type_apport', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutualization_contributions');
    }
};
