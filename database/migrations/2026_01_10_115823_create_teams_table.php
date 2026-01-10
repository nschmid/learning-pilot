<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('personal_team');
            // School-specific fields
            $table->string('school_type')->nullable(); // primary, secondary, vocational, university
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('CH');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('currency')->default('chf');
            $table->string('locale')->default('de');
            $table->string('timezone')->default('Europe/Zurich');
            $table->integer('max_students')->nullable();
            $table->integer('max_instructors')->nullable();
            $table->integer('storage_limit_gb')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
