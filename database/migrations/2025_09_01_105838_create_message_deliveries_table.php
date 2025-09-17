<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('message_campaigns')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('channel', ['in_app', 'email', 'whatsapp', 'sms', 'push']);
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'bounced', 'opened', 'clicked']);
            $table->string('provider_message_id')->nullable();
            $table->text('error')->nullable();
            $table->json('metadata')->nullable(); // Extra provider-specific data
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamps();
            
            $table->index(['campaign_id', 'user_id']);
            $table->index(['user_id', 'channel']);
            $table->index('status');
            $table->index('provider_message_id');
            $table->unique(['campaign_id', 'user_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_deliveries');
    }
};