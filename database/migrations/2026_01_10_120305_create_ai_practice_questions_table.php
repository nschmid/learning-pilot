<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_practice_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('session_id')->constrained('ai_practice_sessions')->cascadeOnDelete();

            // Question content
            $table->string('question_type'); // single_choice, multiple_choice, true_false, fill_blank, short_answer
            $table->text('question_text');
            $table->json('options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->text('explanation');

            // Metadata
            $table->string('difficulty'); // beginner, intermediate, advanced, expert
            $table->json('topics')->nullable();
            $table->json('source_material_ids')->nullable();

            // User response
            $table->text('user_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->unsignedInteger('time_spent_seconds')->nullable();

            // AI feedback
            $table->text('ai_feedback')->nullable();

            // Position
            $table->unsignedSmallInteger('position');
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('is_correct');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_practice_questions');
    }
};
