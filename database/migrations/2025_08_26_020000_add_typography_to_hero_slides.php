<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hero_slides')) {
            return;
        }

        Schema::table('hero_slides', function (Blueprint $table) {
            if (!Schema::hasColumn('hero_slides', 'title_color')) {
                $table->string('title_color', 9)->nullable()->after('subtitle'); // e.g., #FFFFFF or rgba
            }
            if (!Schema::hasColumn('hero_slides', 'subtitle_color')) {
                $table->string('subtitle_color', 9)->nullable()->after('title_color');
            }
            if (!Schema::hasColumn('hero_slides', 'title_size')) {
                $table->unsignedSmallInteger('title_size')->nullable()->after('subtitle_color'); // px
            }
            if (!Schema::hasColumn('hero_slides', 'subtitle_size')) {
                $table->unsignedSmallInteger('subtitle_size')->nullable()->after('title_size'); // px
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('hero_slides')) {
            return;
        }

        Schema::table('hero_slides', function (Blueprint $table) {
            if (Schema::hasColumn('hero_slides', 'subtitle_size')) {
                $table->dropColumn('subtitle_size');
            }
            if (Schema::hasColumn('hero_slides', 'title_size')) {
                $table->dropColumn('title_size');
            }
            if (Schema::hasColumn('hero_slides', 'subtitle_color')) {
                $table->dropColumn('subtitle_color');
            }
            if (Schema::hasColumn('hero_slides', 'title_color')) {
                $table->dropColumn('title_color');
            }
        });
    }
};


