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
        Schema::create('module_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module_name');
            $table->string('module_slug')->unique();
            $table->string('resource_class')->nullable();
            $table->string('page_class')->nullable();
            $table->string('group')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('module_slug');
            $table->index('group');
        });

        // Create role_module_permissions table for custom permissions
        Schema::create('role_module_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('module_permission_id')->constrained('module_permissions')->onDelete('cascade');
            $table->boolean('can_view')->default(false);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_export')->default(false);
            $table->timestamps();

            $table->unique(['role_id', 'module_permission_id'], 'role_module_unique');
            $table->index('role_id');
            $table->index('module_permission_id');
        });

        // Add custom fields to roles table
        Schema::table('roles', function (Blueprint $table) {
            $table->text('description')->nullable()->after('guard_name');
            $table->boolean('is_custom')->default(false)->after('description');
            $table->boolean('is_active')->default(true)->after('is_custom');
            $table->integer('priority')->default(0)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_custom', 'is_active', 'priority']);
        });

        Schema::dropIfExists('role_module_permissions');
        Schema::dropIfExists('module_permissions');
    }
};