<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_user_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained('projets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role_recherche')->nullable();
            $table->unsignedTinyInteger('percentage_load');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start_date', 'end_date']);
            $table->unique(['project_id', 'user_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user_assignments');
    }
};
