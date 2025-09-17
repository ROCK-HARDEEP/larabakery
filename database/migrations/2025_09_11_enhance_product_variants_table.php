<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, add new columns to existing table if it exists
        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                if (!Schema::hasColumn('product_variants', 'variant_type')) {
                    $table->string('variant_type')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('product_variants', 'variant_value')) {
                    $table->string('variant_value')->nullable()->after('variant_type');
                }
                if (!Schema::hasColumn('product_variants', 'weight')) {
                    $table->string('weight')->nullable();
                }
                if (!Schema::hasColumn('product_variants', 'dimensions')) {
                    $table->string('dimensions')->nullable();
                }
                if (!Schema::hasColumn('product_variants', 'image')) {
                    $table->string('image')->nullable();
                }
                if (!Schema::hasColumn('product_variants', 'sort_order')) {
                    $table->integer('sort_order')->default(0);
                }
            });
        } else {
            // Create a new enhanced product_variants table
            Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique();
            $table->string('variant_type'); // e.g., 'flavor', 'size', 'color'
            $table->string('variant_value'); // e.g., 'Chocolate', 'Large', 'Red'
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('weight')->nullable(); // e.g., '500g', '1kg'
            $table->string('dimensions')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['product_id', 'is_active']);
            $table->index('sku');
        });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn(['variant_type', 'variant_value', 'weight', 'dimensions', 'image', 'sort_order']);
            });
        }
    }
};