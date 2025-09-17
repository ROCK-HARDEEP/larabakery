<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
Schema::table('users', function (Blueprint $table) {
if (! Schema::hasColumn('users', 'phone')) {
$table->string('phone')->nullable()->after('email');
}
if (! Schema::hasColumn('users', 'gstin')) {
$table->string('gstin')->nullable()->after('phone');
}
});

if (! Schema::hasTable('addresses')) {
Schema::create('addresses', function (Blueprint $table) {
$table->id();
$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
$table->string('label')->default('Home');
$table->string('line1');
$table->string('line2')->nullable();
$table->string('pincode', 10);
$table->string('city');
$table->string('state_iso', 8)->nullable();
$table->string('country', 2)->default('IN');
$table->boolean('is_default')->default(false);
$table->timestamps();
});
}
}


public function down(): void
{
Schema::dropIfExists('addresses');
Schema::table('users', function (Blueprint $table) {
if (Schema::hasColumn('users', 'phone')) {
$table->dropColumn('phone');
}
if (Schema::hasColumn('users', 'gstin')) {
$table->dropColumn('gstin');
}
});
}
};