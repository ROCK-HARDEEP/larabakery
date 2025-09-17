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
        Schema::table('products', function (Blueprint $table) {
            // Add taste_value field for custom sweetness/spiciness descriptions
            $table->string('taste_value', 100)->nullable()->after('taste_type');

            // Also revert taste_type back to enum for sweetness/spiciness only
            $table->enum('taste_type', ['sweetness', 'spiciness'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('taste_value');
            // Revert taste_type back to varchar
            $table->string('taste_type', 100)->nullable()->change();
        });
    }
};
