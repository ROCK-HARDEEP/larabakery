<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('tracking_number')->nullable()->unique();
            $table->string('carrier')->nullable();
            $table->enum('status', [
                'pending', 
                'processing', 
                'shipped', 
                'in_transit', 
                'out_for_delivery', 
                'delivered', 
                'failed', 
                'returned'
            ])->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('estimated_delivery')->nullable();
            $table->json('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('delivery_proof')->nullable();
            $table->string('delivery_person')->nullable();
            $table->string('delivery_person_contact')->nullable();
            $table->timestamps();

            $table->index('tracking_number');
            $table->index('status');
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};