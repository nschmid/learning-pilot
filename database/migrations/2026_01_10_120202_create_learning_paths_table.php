<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_paths', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('creator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('team_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('objectives')->nullable();
            $table->string('difficulty')->default('beginner');
            $table->string('thumbnail')->nullable();
            $table->integer('estimated_hours')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('version')->default(1);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'is_featured']);
            $table->index('creator_id');
            $table->index('team_id');
            $table->index('category_id');
            $table->index('difficulty');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_paths');
    }
};
