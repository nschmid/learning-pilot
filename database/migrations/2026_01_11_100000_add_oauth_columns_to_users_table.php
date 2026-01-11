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
        Schema::table('users', function (Blueprint $table) {
            // Google OAuth
            $table->string('oauth_google_id')->nullable()->unique()->after('password');
            $table->text('oauth_google_token')->nullable()->after('oauth_google_id');
            $table->text('oauth_google_refresh_token')->nullable()->after('oauth_google_token');

            // Microsoft OAuth (for schools)
            $table->string('oauth_microsoft_id')->nullable()->unique()->after('oauth_google_refresh_token');
            $table->text('oauth_microsoft_token')->nullable()->after('oauth_microsoft_id');
            $table->text('oauth_microsoft_refresh_token')->nullable()->after('oauth_microsoft_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'oauth_google_id',
                'oauth_google_token',
                'oauth_google_refresh_token',
                'oauth_microsoft_id',
                'oauth_microsoft_token',
                'oauth_microsoft_refresh_token',
            ]);
        });
    }
};
