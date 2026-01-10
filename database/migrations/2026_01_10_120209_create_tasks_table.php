<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('step_id')->constrained('learning_steps')->cascadeOnDelete();
            $table->string('task_type'); // submission, project, discussion
            $table->string('title');
            $table->longText('instructions');
            $table->integer('max_points')->default(100);
            $table->integer('due_days')->nullable();
            $table->boolean('allow_late')->default(true);
            $table->boolean('allow_resubmit')->default(true);
            $table->json('rubric')->nullable();
            $table->json('allowed_file_types')->nullable();
            $table->integer('max_file_size_mb')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
