<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'phone_verification_code')) {
                $table->string('phone_verification_code')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'pincode')) {
                $table->string('pincode')->nullable();
            }
            if (!Schema::hasColumn('users', 'pincode_verified')) {
                $table->boolean('pincode_verified')->default(false);
            }
            if (!Schema::hasColumn('users', 'verification_skipped')) {
                $table->boolean('verification_skipped')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'phone_verification_code')) {
                $table->dropColumn('phone_verification_code');
            }
            if (Schema::hasColumn('users', 'phone_verified_at')) {
                $table->dropColumn('phone_verified_at');
            }
            if (Schema::hasColumn('users', 'pincode')) {
                $table->dropColumn('pincode');
            }
            if (Schema::hasColumn('users', 'pincode_verified')) {
                $table->dropColumn('pincode_verified');
            }
            if (Schema::hasColumn('users', 'verification_skipped')) {
                $table->dropColumn('verification_skipped');
            }
        });
    }
};
