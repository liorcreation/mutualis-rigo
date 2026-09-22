<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_reservations', function (Blueprint $table): void {
            $table->foreignId('validated_by')->nullable()->after('commentaire_validation')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable()->after('validated_by');
            $table->text('decision_reason')->nullable()->after('validated_at');
        });
    }

    public function down(): void
    {
        Schema::table('material_reservations', function (Blueprint $table): void {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['validated_by', 'validated_at', 'decision_reason']);
        });
    }
};
