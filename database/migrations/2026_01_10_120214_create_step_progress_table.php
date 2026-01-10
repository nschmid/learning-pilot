<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('step_progress', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('step_id')->constrained('learning_steps')->cascadeOnDelete();
            $table->string('status')->default('not_started'); // not_started, in_progress, completed, skipped
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->integer('points_earned')->default(0);
            $table->integer('attempts')->default(0);
            $table->json('data')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'step_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('step_progress');
    }
};
