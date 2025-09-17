<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subject')->nullable();
            $table->text('body_template');
            $table->json('channels')->default('[]'); // ['in_app', 'email', 'whatsapp', 'sms', 'push']
            $table->json('filters')->nullable(); // User targeting filters
            $table->timestamp('schedule_at')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            
            // Statistics
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('schedule_at');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_campaigns');
    }
};