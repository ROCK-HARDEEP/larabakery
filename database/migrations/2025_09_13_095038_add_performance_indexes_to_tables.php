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
        // Add indexes to homepage_faqs table
        Schema::table('homepage_faqs', function (Blueprint $table) {
            $table->index('is_active', 'idx_homepage_faqs_active');
            $table->index('order_index', 'idx_homepage_faqs_order');
            $table->index(['is_active', 'order_index'], 'idx_homepage_faqs_active_order');
        });

        // Add indexes to product_faqs table
        Schema::table('product_faqs', function (Blueprint $table) {
            $table->index('is_active', 'idx_product_faqs_active');
            $table->index('order_index', 'idx_product_faqs_order');
            $table->index(['is_active', 'order_index'], 'idx_product_faqs_active_order');
            $table->index(['product_id', 'is_active', 'order_index'], 'idx_product_faqs_product_active_order');
        });

        // Add indexes to products table for sorting/filtering
        Schema::table('products', function (Blueprint $table) {
            $table->index('rating', 'idx_products_rating');
            $table->index('base_price', 'idx_products_price');
            $table->index(['rating', 'is_active'], 'idx_products_rating_active');
            $table->index(['base_price', 'is_active'], 'idx_products_price_active');
            $table->index('discount_percentage', 'idx_products_discount');
            $table->index(['has_discount', 'is_active'], 'idx_products_has_discount_active');
        });

        // Add indexes to notifications table for better performance
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['notifiable_type', 'notifiable_id', 'created_at'], 'idx_notifications_notifiable_created');
                $table->index('read_at', 'idx_notifications_read_at');
            });
        }

        // Add indexes to media table if it exists
        if (Schema::hasTable('media')) {
            Schema::table('media', function (Blueprint $table) {
                $table->index(['model_type', 'model_id', 'collection_name'], 'idx_media_model_collection');
            });
        }

        // Add indexes to blogs table for better performance
        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->index('published_at', 'idx_blogs_published');
                $table->index(['is_featured', 'is_active', 'published_at'], 'idx_blogs_featured_active_published');
            });
        }

        // Add indexes to testimonials table
        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->index(['is_active', 'rating'], 'idx_testimonials_active_rating');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop homepage_faqs indexes
        Schema::table('homepage_faqs', function (Blueprint $table) {
            $table->dropIndex('idx_homepage_faqs_active');
            $table->dropIndex('idx_homepage_faqs_order');
            $table->dropIndex('idx_homepage_faqs_active_order');
        });

        // Drop product_faqs indexes
        Schema::table('product_faqs', function (Blueprint $table) {
            $table->dropIndex('idx_product_faqs_active');
            $table->dropIndex('idx_product_faqs_order');
            $table->dropIndex('idx_product_faqs_active_order');
            $table->dropIndex('idx_product_faqs_product_active_order');
        });

        // Drop products indexes
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_rating');
            $table->dropIndex('idx_products_price');
            $table->dropIndex('idx_products_rating_active');
            $table->dropIndex('idx_products_price_active');
            $table->dropIndex('idx_products_discount');
            $table->dropIndex('idx_products_has_discount_active');
        });

        // Drop notifications indexes
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('idx_notifications_notifiable_created');
                $table->dropIndex('idx_notifications_read_at');
            });
        }

        // Drop media indexes
        if (Schema::hasTable('media')) {
            Schema::table('media', function (Blueprint $table) {
                $table->dropIndex('idx_media_model_collection');
            });
        }

        // Drop blogs indexes
        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropIndex('idx_blogs_published');
                $table->dropIndex('idx_blogs_featured_active_published');
            });
        }

        // Drop testimonials indexes
        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->dropIndex('idx_testimonials_active_rating');
            });
        }
    }
};