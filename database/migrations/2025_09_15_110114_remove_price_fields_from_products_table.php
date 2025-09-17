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
        Schema::table('products', function (Blueprint $table) {
            // Remove price-related columns
            $table->dropColumn([
                'base_price',
                'discount_price',
                'discount_percentage',
                'discount_start_date',
                'discount_end_date',
                'has_discount',
                'stock' // Remove common stock as we'll use variant stock
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add back the price-related columns
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->date('discount_start_date')->nullable();
            $table->date('discount_end_date')->nullable();
            $table->boolean('has_discount')->default(false);
            $table->integer('stock')->default(0);
        });
    }
};