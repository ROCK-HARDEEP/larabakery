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
            // Taste type: 'sweetness' or 'spiciness' (can be null if not applicable)
            $table->enum('taste_type', ['sweetness', 'spiciness'])->nullable()->after('review_count');

            // Taste level: 1-5 scale (1=mild, 5=intense)
            $table->tinyInteger('taste_level')->nullable()->after('taste_type');

            // Taste description (optional custom description)
            $table->string('taste_description', 255)->nullable()->after('taste_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['taste_type', 'taste_level', 'taste_description']);
        });
    }
};
