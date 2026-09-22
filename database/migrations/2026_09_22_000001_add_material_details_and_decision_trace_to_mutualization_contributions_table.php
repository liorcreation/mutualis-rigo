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
            $table->string('materiel_nom')->nullable()->after('description_apport');
            $table->unsignedInteger('materiel_quantite_disponible')->nullable()->after('materiel_nom');
            $table->string('materiel_unite', 40)->nullable()->after('materiel_quantite_disponible');
            $table->string('materiel_etat', 80)->nullable()->after('materiel_unite');
            $table->string('materiel_localisation')->nullable()->after('materiel_etat');
            $table->date('materiel_disponible_du')->nullable()->after('materiel_localisation');
            $table->date('materiel_disponible_au')->nullable()->after('materiel_disponible_du');
            $table->foreignId('validated_by')->nullable()->after('commentaire_validation')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable()->after('validated_by');
            $table->text('decision_reason')->nullable()->after('validated_at');

            $table->index(['type_apport', 'statut', 'materiel_nom']);
        });
    }

    public function down(): void
    {
        Schema::table('mutualization_contributions', function (Blueprint $table): void {
            $table->dropForeign(['validated_by']);
            $table->dropIndex(['type_apport', 'statut', 'materiel_nom']);
            $table->dropColumn([
                'materiel_nom',
                'materiel_quantite_disponible',
                'materiel_unite',
                'materiel_etat',
                'materiel_localisation',
                'materiel_disponible_du',
                'materiel_disponible_au',
                'validated_by',
                'validated_at',
                'decision_reason',
            ]);
        });
    }
};
