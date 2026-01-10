<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('step_id')->constrained('learning_steps')->cascadeOnDelete();
            $table->string('material_type'); // text, video, audio, pdf, image, link, interactive
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->string('external_url')->nullable();
            $table->string('video_source_type')->nullable(); // upload, youtube, loom, vimeo
            $table->string('video_source_id')->nullable(); // YouTube video ID, Loom ID, etc.
            $table->integer('position')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['step_id', 'position']);
            $table->index('material_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_materials');
    }
};
