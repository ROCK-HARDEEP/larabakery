<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add indexes for frequently queried columns
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasIndex('products', 'products_is_active_index')) {
                $table->index('is_active');
            }
            if (!Schema::hasIndex('products', 'products_category_id_is_active_index')) {
                $table->index(['category_id', 'is_active']);
            }
            if (!Schema::hasIndex('products', 'products_slug_index')) {
                $table->index('slug');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasIndex('categories', 'categories_is_active_position_index')) {
                $table->index(['is_active', 'position']);
            }
            if (!Schema::hasIndex('categories', 'categories_slug_index')) {
                $table->index('slug');
            }
        });

        Schema::table('limited_time_offers', function (Blueprint $table) {
            if (!Schema::hasIndex('limited_time_offers', 'limited_time_offers_is_active_starts_at_ends_at_index')) {
                $table->index(['is_active', 'starts_at', 'ends_at']);
            }
        });

        Schema::table('hero_slides', function (Blueprint $table) {
            if (!Schema::hasIndex('hero_slides', 'hero_slides_is_active_sort_order_index')) {
                $table->index(['is_active', 'sort_order']);
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasIndex('orders', 'orders_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
            if (!Schema::hasIndex('orders', 'orders_created_at_index')) {
                $table->index('created_at');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasIndex('order_items', 'order_items_order_id_index')) {
                $table->index('order_id');
            }
            if (!Schema::hasIndex('order_items', 'order_items_product_id_index')) {
                $table->index('product_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['category_id', 'is_active']);
            $table->dropIndex(['slug']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'position']);
            $table->dropIndex(['slug']);
        });

        Schema::table('limited_time_offers', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'starts_at', 'ends_at']);
        });

        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'sort_order']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
        });
    }
};