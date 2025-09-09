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
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // e.g., toys, books, clothes
            $table->decimal('price_range_min', 8, 2)->nullable();
            $table->decimal('price_range_max', 8, 2)->nullable();
            $table->string('store_suggestion')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('priority')->default(3); // 1=high, 2=medium, 3=low
            $table->boolean('is_claimed')->default(false);
            $table->string('claimed_by_name')->nullable();
            $table->string('claimed_by_email')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->text('notes')->nullable(); // Admin notes
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['is_active', 'is_claimed']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};
