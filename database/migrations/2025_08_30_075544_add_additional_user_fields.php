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
        Schema::table('users', function (Blueprint $table) {
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'phone_verification_code')) {
                $table->string('phone_verification_code', 6)->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('login_ip');
            }
            if (!Schema::hasColumn('users', 'pincode')) {
                $table->string('pincode', 10)->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'pincode_verified_at')) {
                $table->timestamp('pincode_verified_at')->nullable()->after('pincode');
            }
            
            // Add indexes for performance (if columns exist)
            if (Schema::hasColumn('users', 'phone_verification_code')) {
                $table->index('phone_verification_code');
            }
            if (Schema::hasColumn('users', 'pincode')) {
                $table->index('pincode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone_verification_code']);
            $table->dropIndex(['pincode']);
            
            $table->dropColumn([
                'phone_verified_at',
                'phone_verification_code',
                'address',
                'pincode',
                'pincode_verified_at'
            ]);
        });
    }
};
