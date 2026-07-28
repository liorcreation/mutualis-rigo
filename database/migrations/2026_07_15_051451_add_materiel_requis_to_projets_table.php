<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('projets', function (Blueprint $table) {
        $table->text('materiel_requis')->nullable()->after('budget');
    });
}

public function down(): void
{
    Schema::table('projets', function (Blueprint $table) {
        $table->dropColumn('materiel_requis');
    });
}
};
