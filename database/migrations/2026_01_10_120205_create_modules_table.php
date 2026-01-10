<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('learning_path_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('position')->default(0);
            $table->string('unlock_condition')->default('sequential');
            $table->integer('unlock_value')->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['learning_path_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
