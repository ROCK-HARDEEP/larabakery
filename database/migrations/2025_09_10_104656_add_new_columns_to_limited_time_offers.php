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
        Schema::table('limited_time_offers', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('limited_time_offers', 'product_ids')) {
                $table->json('product_ids')->nullable()->after('price_rule_json');
            }
            if (!Schema::hasColumn('limited_time_offers', 'max_quantity')) {
                $table->integer('max_quantity')->nullable()->after('ends_at');
            }
            if (!Schema::hasColumn('limited_time_offers', 'sold_quantity')) {
                $table->integer('sold_quantity')->default(0)->after('max_quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('limited_time_offers', function (Blueprint $table) {
            $table->dropColumn(['product_ids', 'max_quantity', 'sold_quantity']);
        });
    }
};