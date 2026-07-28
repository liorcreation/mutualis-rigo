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
            $table->text('commentaire_validation')->nullable()->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('mutualization_contributions', function (Blueprint $table): void {
            $table->dropColumn('commentaire_validation');
        });
    }
};
