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
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id();
            
            // Get In Touch Section
            $table->string('get_in_touch_image')->nullable();
            $table->string('get_in_touch_title')->nullable();
            $table->text('get_in_touch_quote')->nullable();
            
            // Contact Information Section
            $table->text('contact_address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->json('business_hours')->nullable();
            
            // Follow Us Section
            $table->json('social_media_links')->nullable();
            
            // Find Us Section
            $table->text('map_embed_link')->nullable();
            $table->string('map_image')->nullable();
            
            // FAQ Section
            $table->json('faqs')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_us');
    }
};