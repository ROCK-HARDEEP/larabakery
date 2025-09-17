<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('custom_sections', function (Blueprint $table) {
            $table->string('button_text')->nullable()->after('image');
            $table->string('button_link')->nullable()->after('button_text');
            $table->string('button_style')->nullable()->after('button_link');
        });
    }

    public function down()
    {
        Schema::table('custom_sections', function (Blueprint $table) {
            $table->dropColumn(['button_text', 'button_link', 'button_style']);
        });
    }
};