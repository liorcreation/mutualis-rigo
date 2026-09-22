<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mutualization_contributions', function (Blueprint $table): void {
            $table->string('competence_nom')->nullable()->after('description_apport');
            $table->string('competence_niveau', 60)->nullable()->after('competence_nom');

            $table->index(['type_apport', 'competence_nom', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::table('mutualization_contributions', function (Blueprint $table): void {
            $table->dropIndex(['type_apport', 'competence_nom', 'statut']);
            $table->dropColumn(['competence_nom', 'competence_niveau']);
        });
    }
};
