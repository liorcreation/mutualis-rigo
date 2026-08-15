<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table): void {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('project_id')->constrained('projets')->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('contribution_id')->unique()->constrained('mutualization_contributions')->restrictOnDelete();
            $table->string('status')->default('draft');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('XOF');
            $table->timestamp('terms_accepted_at')->nullable();
            $table->string('signature_ip')->nullable();
            $table->string('document_hash')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Suppression logique : conserve le contrat comme pièce probante (paiements liés)

            $table->index(['project_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
