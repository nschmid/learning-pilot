<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('step_id')->constrained('learning_steps')->cascadeOnDelete();
            $table->string('assessment_type'); // quiz, exam, survey
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('time_limit_minutes')->nullable();
            $table->integer('passing_score_percent')->default(70);
            $table->integer('max_attempts')->nullable();
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_answers')->default(false);
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('show_score_immediately')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
