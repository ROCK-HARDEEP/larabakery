<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('filters'); // Reusable filter criteria
            $table->integer('estimated_count')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_segments');
    }
};