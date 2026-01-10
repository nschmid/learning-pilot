<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prerequisites', function (Blueprint $table) {
            $table->foreignUuid('learning_path_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('required_path_id')->constrained('learning_paths')->cascadeOnDelete();
            $table->primary(['learning_path_id', 'required_path_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prerequisites');
    }
};
