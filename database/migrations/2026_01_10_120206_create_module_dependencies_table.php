<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_dependencies', function (Blueprint $table) {
            $table->foreignUuid('module_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('required_module_id')->constrained('modules')->cascadeOnDelete();
            $table->primary(['module_id', 'required_module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_dependencies');
    }
};
