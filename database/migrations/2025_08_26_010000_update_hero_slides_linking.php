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
            if (!Schema::hasColumn('hero_slides', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('button_label')
                    ->constrained()
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('hero_slides', 'product_id')) {
                $table->foreignId('product_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained()
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            }

            if (Schema::hasColumn('hero_slides', 'link_type')) {
                $table->dropColumn('link_type');
            }
            if (Schema::hasColumn('hero_slides', 'route_name')) {
                $table->dropColumn('route_name');
            }
            if (Schema::hasColumn('hero_slides', 'route_params')) {
                $table->dropColumn('route_params');
            }
            if (Schema::hasColumn('hero_slides', 'url')) {
                $table->dropColumn('url');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('hero_slides')) {
            return;
        }

        Schema::table('hero_slides', function (Blueprint $table) {
            if (Schema::hasColumn('hero_slides', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }
            if (Schema::hasColumn('hero_slides', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }

            if (!Schema::hasColumn('hero_slides', 'link_type')) {
                $table->enum('link_type', ['route','url'])->default('route')->after('button_label');
            }
            if (!Schema::hasColumn('hero_slides', 'route_name')) {
                $table->string('route_name')->nullable()->after('link_type');
            }
            if (!Schema::hasColumn('hero_slides', 'route_params')) {
                $table->json('route_params')->nullable()->after('route_name');
            }
            if (!Schema::hasColumn('hero_slides', 'url')) {
                $table->string('url')->nullable()->after('route_params');
            }
        });
    }
};


