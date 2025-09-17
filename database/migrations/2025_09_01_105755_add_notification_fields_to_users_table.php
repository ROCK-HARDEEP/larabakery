<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Notification preferences
            $table->boolean('notify_in_app')->default(true)->after('password');
            $table->boolean('notify_email')->default(true)->after('notify_in_app');
            $table->boolean('notify_whatsapp')->default(false)->after('notify_email');
            $table->boolean('notify_sms')->default(false)->after('notify_whatsapp');
            $table->boolean('notify_push')->default(false)->after('notify_sms');
            
            // Contact fields
            $table->string('fcm_token', 500)->nullable()->after('notify_push');
            $table->string('wa_number', 20)->nullable()->after('fcm_token');
            $table->string('sms_number', 20)->nullable()->after('wa_number');
            $table->string('avatar_url')->nullable()->after('sms_number');
            
            // Custom tags for segmentation
            $table->json('tags')->nullable()->after('avatar_url');
            
            $table->index('notify_in_app');
            $table->index('notify_email');
            $table->index('notify_whatsapp');
            $table->index('notify_sms');
            $table->index('notify_push');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notify_in_app',
                'notify_email',
                'notify_whatsapp',
                'notify_sms',
                'notify_push',
                'fcm_token',
                'wa_number',
                'sms_number',
                'avatar_url',
                'tags'
            ]);
        });
    }
};