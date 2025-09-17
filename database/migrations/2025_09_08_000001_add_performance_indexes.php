<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add performance indexes for dashboard queries
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['created_at', 'status'], 'orders_created_status_idx');
            $table->index(['created_at', 'total'], 'orders_created_total_idx');
            $table->index(['user_id', 'created_at'], 'orders_user_created_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['created_at'], 'users_created_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['stock', 'is_active'], 'products_stock_active_idx');
            $table->index(['is_active', 'created_at'], 'products_active_created_idx');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_created_status_idx');
            $table->dropIndex('orders_created_total_idx');
            $table->dropIndex('orders_user_created_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_created_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_stock_active_idx');
            $table->dropIndex('products_active_created_idx');
        });
    }
};