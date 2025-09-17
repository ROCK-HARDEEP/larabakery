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
        Schema::table('contact_us', function (Blueprint $table) {
            $table->string('map_latitude')->nullable()->after('social_media_links');
            $table->string('map_longitude')->nullable()->after('map_latitude');
            $table->text('map_address')->nullable()->after('map_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_us', function (Blueprint $table) {
            $table->dropColumn(['map_latitude', 'map_longitude', 'map_address']);
        });
    }
};