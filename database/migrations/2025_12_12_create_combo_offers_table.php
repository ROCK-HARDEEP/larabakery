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
        Schema::create('combo_offers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('original_price', 10, 2);
            $table->decimal('combo_price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('max_quantity')->nullable();
            $table->integer('sold_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
            $table->index('display_order');
        });
        
        // Create combo_offer_products pivot table
        Schema::create('combo_offer_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_offer_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['combo_offer_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_offer_products');
        Schema::dropIfExists('combo_offers');
    }
};