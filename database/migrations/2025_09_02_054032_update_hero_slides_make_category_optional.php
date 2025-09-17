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
        Schema::table('hero_slides', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['category_id']);
            
            // Make category_id nullable
            $table->foreignId('category_id')->nullable()->change();
            
            // Add the new foreign key constraint with nullOnDelete
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['category_id']);
            
            // Make category_id required again
            $table->foreignId('category_id')->nullable(false)->change();
            
            // Add the original foreign key constraint
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
        });
    }
};
