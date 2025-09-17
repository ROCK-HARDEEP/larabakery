<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add image field to categories table
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
        });

        // Add images_path field to products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'images_path')) {
                $table->json('images_path')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        // Remove image field from categories table
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'image')) {
                $table->dropColumn('image');
            }
        });

        // Remove images_path field from products table
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'images_path')) {
                $table->dropColumn('images_path');
            }
        });
    }
};
