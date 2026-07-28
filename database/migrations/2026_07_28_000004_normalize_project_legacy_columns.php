<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('projets')->whereIn('statut', ['en_attente', 'En attente'])->update([
            'statut' => 'en_etude',
        ]);
        DB::table('projets')->whereIn('statut', ['valide', 'Validé'])->update([
            'statut' => 'en_cours_de_mutualisation',
        ]);
        DB::table('projets')->whereIn('statut', ['refuse', 'Rejeté'])->update([
            'statut' => 'brouillon',
        ]);

        Schema::table('projets', function (Blueprint $table): void {
            $table->decimal('budget', 15, 2)->nullable()->change();
            $table->string('statut')->default('brouillon')->change();
        });
    }

    public function down(): void
    {
        Schema::table('projets', function (Blueprint $table): void {
            $table->decimal('budget', 15, 2)->nullable(false)->change();
            $table->string('statut')->default('en_attente')->change();
        });
    }
};
