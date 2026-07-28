<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projets', function (Blueprint $table): void {
            $table->string('categorie')->nullable()->after('description');
            $table->decimal('besoin_financier_target', 15, 2)->nullable()->after('categorie');
            $table->decimal('besoin_financier_actuel', 15, 2)->default(0)->after('besoin_financier_target');
            $table->json('besoins_competences')->nullable()->after('besoin_financier_actuel');
            $table->json('besoins_materiels')->nullable()->after('besoins_competences');
        });
    }

    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table): void {
            $table->dropColumn([
                'categorie',
                'besoin_financier_target',
                'besoin_financier_actuel',
                'besoins_competences',
                'besoins_materiels',
            ]);
        });
    }
};
