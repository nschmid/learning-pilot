<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('enrollment_id')->constrained()->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->decimal('score_percent', 5, 2)->nullable();
            $table->integer('points_earned')->default(0);
            $table->boolean('passed')->default(false);
            $table->integer('time_spent_seconds')->nullable();
            $table->json('answers')->nullable();
            $table->timestamps();

            $table->index(['assessment_id', 'enrollment_id']);
            $table->index('passed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_attempts');
    }
};
