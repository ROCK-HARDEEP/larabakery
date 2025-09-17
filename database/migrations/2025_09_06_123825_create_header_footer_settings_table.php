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
        Schema::create('header_footer_settings', function (Blueprint $table) {
            $table->id();
            
            // Header Settings
            $table->string('header_logo')->nullable();
            $table->string('header_brand_name')->nullable();
            
            // Footer Settings
            $table->string('footer_logo')->nullable();
            $table->string('footer_brand_name')->nullable();
            $table->text('footer_description')->nullable();
            $table->string('footer_background_color')->default('#1a1a1a');
            $table->string('footer_text_color')->default('#ffffff');
            
            // Links (JSON)
            $table->json('social_media_links')->nullable();
            $table->json('quick_links')->nullable();
            $table->json('category_links')->nullable();
            $table->json('customer_service_links')->nullable();
            
            // Contact Information
            $table->text('contact_address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_hours')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_footer_settings');
    }
};