<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('path_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('learning_path_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating');
            $table->text('review_text')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'learning_path_id']);
            $table->index(['learning_path_id', 'is_approved', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('path_reviews');
    }
};
