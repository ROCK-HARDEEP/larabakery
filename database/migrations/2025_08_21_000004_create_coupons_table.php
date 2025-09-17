<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;  
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
Schema::create('coupons', function (Blueprint $table) {
$table->id();
$table->string('code')->unique();
$table->enum('type', ['flat','percent']);
$table->decimal('value', 10, 2);
$table->decimal('min_cart', 10, 2)->default(0);
$table->unsignedInteger('usage_limit')->nullable();
$table->unsignedInteger('used_count')->default(0);
$table->timestamp('expires_at')->nullable();
$table->boolean('is_active')->default(true);
$table->timestamps();
});
}
public function down(): void { Schema::dropIfExists('coupons'); }
};