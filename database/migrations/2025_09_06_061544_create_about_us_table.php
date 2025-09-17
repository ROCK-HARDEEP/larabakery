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
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            
            // Our Story Section
            $table->string('story_image')->nullable();
            $table->string('story_title')->nullable();
            $table->text('story_content')->nullable();
            
            // How It All Began Section
            $table->string('began_title')->nullable();
            $table->string('began_quote')->nullable();
            $table->text('began_content')->nullable();
            $table->string('years_experience')->nullable();
            $table->string('happy_customers')->nullable();
            
            // Our Values Section (JSON array)
            $table->json('values')->nullable();
            
            // Meet Our Team Section (JSON array)
            $table->json('team_members')->nullable();
            
            // Ready to Taste Section
            $table->string('cta_section_color')->default('#000000');
            $table->string('cta_title')->nullable();
            $table->string('cta_subtitle')->nullable();
            $table->string('cta_button_text')->default('Shop Our Products');
            $table->string('cta_button_link')->default('/products');
            $table->string('cta_button_color')->default('#FF6B00');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
