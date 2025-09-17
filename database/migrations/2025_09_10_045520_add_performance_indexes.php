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
        // Add composite index for orders status and created_at for better performance
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'orders_status_created_idx');
        });
        
        // Add index for orders user_id for faster joins
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id', 'orders_user_id_idx');
        });
        
        // Add index for products category_id for faster joins
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id', 'products_category_id_idx');
        });
        
        // Add index for order_items order_id for faster joins
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id', 'order_items_order_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_created_idx');
            $table->dropIndex('orders_user_id_idx');
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_category_id_idx');
        });
        
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_order_id_idx');
        });
    }
};
