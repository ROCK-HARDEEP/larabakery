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
        Schema::table('header_footer_settings', function (Blueprint $table) {
            $table->boolean('announcement_bar_enabled')->default(true)->after('header_brand_name');
            $table->text('announcement_bar_text')->nullable()->after('announcement_bar_enabled');
            $table->string('announcement_bar_bg_color')->default('#f69d1c')->after('announcement_bar_text');
            $table->string('announcement_bar_text_color')->default('#ffffff')->after('announcement_bar_bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('header_footer_settings', function (Blueprint $table) {
            $table->dropColumn([
                'announcement_bar_enabled',
                'announcement_bar_text',
                'announcement_bar_bg_color',
                'announcement_bar_text_color'
            ]);
        });
    }
};