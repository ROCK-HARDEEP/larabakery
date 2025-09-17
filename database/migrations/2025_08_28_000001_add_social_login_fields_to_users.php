<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Social login fields
            $table->string('google_id')->nullable()->after('password');
            $table->string('facebook_id')->nullable()->after('google_id');
            $table->string('provider')->nullable()->after('facebook_id');
            $table->string('provider_token', 500)->nullable()->after('provider');
            $table->string('username')->nullable()->unique()->after('name');
            $table->json('social_data')->nullable()->after('provider_token');
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            $table->string('login_ip')->nullable()->after('last_login_at');
            
            // Indexes for faster queries
            $table->index('google_id');
            $table->index('facebook_id');
            $table->index('provider');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['google_id']);
            $table->dropIndex(['facebook_id']);
            $table->dropIndex(['provider']);
            
            $table->dropColumn([
                'google_id',
                'facebook_id',
                'provider',
                'provider_token',
                'username',
                'social_data',
                'last_login_at',
                'login_ip',
            ]);
        });
    }
};