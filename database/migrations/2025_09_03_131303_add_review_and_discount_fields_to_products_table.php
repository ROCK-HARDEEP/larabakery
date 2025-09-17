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
            // Review fields
            $table->decimal('rating', 3, 2)->default(0.00)->after('stock'); // 0.00 to 5.00
            $table->integer('review_count')->default(0)->after('rating'); // Number of reviews
            
            // Discount fields
            $table->decimal('discount_price', 10, 2)->nullable()->after('base_price'); // Discounted price
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount_price'); // Discount percentage
            $table->boolean('has_discount')->default(false)->after('discount_percentage'); // Whether product has discount
            $table->date('discount_start_date')->nullable()->after('has_discount'); // Discount start date
            $table->date('discount_end_date')->nullable()->after('discount_start_date'); // Discount end date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'rating',
                'review_count',
                'discount_price',
                'discount_percentage',
                'has_discount',
                'discount_start_date',
                'discount_end_date'
            ]);
        });
    }
};
