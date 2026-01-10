<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_generated_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Polymorphic relationship
            $table->string('contentable_type');
            $table->uuid('contentable_id');

            // Content type
            $table->string('content_type'); // explanation, hint, summary, practice_question, feedback, recommendation, flashcard, concept_breakdown

            // Generated content
            $table->text('content');
            $table->json('content_metadata')->nullable();
            $table->json('context_snapshot');

            // User feedback
            $table->unsignedTinyInteger('rating')->nullable();
            $table->boolean('was_helpful')->nullable();
            $table->text('user_feedback')->nullable();

            // Caching
            $table->string('cache_key')->unique()->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('version')->default(1);

            // Audit
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Indexes
            $table->index(['contentable_type', 'contentable_id']);
            $table->index('content_type');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generated_contents');
    }
};
