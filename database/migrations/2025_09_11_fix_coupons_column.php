<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->renameColumn('used_count', 'usage_count');
        });
        
        // Add missing columns if they don't exist
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'discount_type')) {
                $table->enum('discount_type', ['percentage', 'fixed'])->after('type');
            }
            if (!Schema::hasColumn('coupons', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)->after('discount_type');
            }
            if (!Schema::hasColumn('coupons', 'minimum_order_amount')) {
                $table->decimal('minimum_order_amount', 10, 2)->nullable()->after('discount_value');
            }
            if (!Schema::hasColumn('coupons', 'product_ids')) {
                $table->json('product_ids')->nullable()->after('minimum_order_amount');
            }
            if (!Schema::hasColumn('coupons', 'usage_limit_per_customer')) {
                $table->integer('usage_limit_per_customer')->nullable()->after('usage_count');
            }
            if (!Schema::hasColumn('coupons', 'description')) {
                $table->text('description')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->renameColumn('usage_count', 'used_count');
            $table->dropColumn(['discount_type', 'discount_value', 'minimum_order_amount', 'product_ids', 'usage_limit_per_customer', 'description']);
        });
    }
};