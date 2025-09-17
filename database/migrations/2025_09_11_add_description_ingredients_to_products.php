<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'full_description')) {
                $table->text('full_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'ingredients')) {
                $table->text('ingredients')->nullable()->after('full_description');
            }
            if (!Schema::hasColumn('products', 'nutritional_info')) {
                $table->json('nutritional_info')->nullable()->after('ingredients');
            }
            if (!Schema::hasColumn('products', 'allergen_info')) {
                $table->text('allergen_info')->nullable()->after('nutritional_info');
            }
            if (!Schema::hasColumn('products', 'storage_instructions')) {
                $table->text('storage_instructions')->nullable()->after('allergen_info');
            }
            if (!Schema::hasColumn('products', 'shelf_life')) {
                $table->string('shelf_life')->nullable()->after('storage_instructions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'full_description',
                'ingredients',
                'nutritional_info',
                'allergen_info',
                'storage_instructions',
                'shelf_life'
            ]);
        });
    }
};