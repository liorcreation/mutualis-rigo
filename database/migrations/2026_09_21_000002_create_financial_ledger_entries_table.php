<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_ledger_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('source_project_id')->nullable()->constrained('projets')->nullOnDelete();
            $table->foreignId('destination_project_id')->nullable()->constrained('projets')->nullOnDelete();
            $table->string('entry_type');
            $table->decimal('amount', 15, 2);
            $table->string('reference')->unique();
            $table->json('metadata')->nullable();
            $table->string('hash_parent');
            $table->string('hash_actuel');
            $table->timestamp('enregistre_le');
            $table->timestamps();

            $table->index(['entry_type', 'enregistre_le']);
            $table->index(['source_project_id', 'destination_project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_ledger_entries');
    }
};
