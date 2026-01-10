<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_practice_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();

            // Scope
            $table->foreignUuid('learning_path_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('module_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('step_id')->nullable()->constrained('learning_steps')->nullOnDelete();

            // Session config
            $table->string('difficulty')->default('adaptive'); // beginner, intermediate, advanced, adaptive
            $table->unsignedSmallInteger('question_count')->default(10);
            $table->json('focus_areas')->nullable();

            // Progress
            $table->unsignedSmallInteger('questions_generated')->default(0);
            $table->unsignedSmallInteger('questions_answered')->default(0);
            $table->unsignedSmallInteger('correct_answers')->default(0);

            // Status
            $table->string('status')->default('active'); // active, completed, abandoned
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_practice_sessions');
    }
};
