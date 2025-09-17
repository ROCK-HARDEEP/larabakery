<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
Schema::create('bundles', function (Blueprint $table) {
$table->id();
$table->string('name');
$table->string('slug')->unique();
$table->json('price_rule_json')->nullable(); // e.g., fixed price or % off
$table->boolean('is_active')->default(true);
$table->timestamps();
});


Schema::create('bundle_items', function (Blueprint $table) {
$table->id();
$table->foreignId('bundle_id')->constrained('bundles')->cascadeOnDelete();
$table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
$table->unsignedInteger('qty')->default(1);
$table->timestamps();
$table->unique(['bundle_id','product_id']);
});
}


public function down(): void
{ Schema::dropIfExists('bundle_items'); Schema::dropIfExists('bundles'); }
};