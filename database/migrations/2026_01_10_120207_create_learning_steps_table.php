<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('step_type'); // material, task, assessment
            $table->integer('position')->default(0);
            $table->integer('points_value')->default(10);
            $table->integer('estimated_minutes')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_preview')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['module_id', 'position']);
            $table->index('step_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_steps');
    }
};
