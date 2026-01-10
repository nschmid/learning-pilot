<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assessment_id')->constrained()->cascadeOnDelete();
            $table->string('question_type'); // single_choice, multiple_choice, true_false, text, matching
            $table->text('question_text');
            $table->string('question_image')->nullable();
            $table->text('explanation')->nullable();
            $table->integer('points')->default(1);
            $table->integer('position')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['assessment_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
