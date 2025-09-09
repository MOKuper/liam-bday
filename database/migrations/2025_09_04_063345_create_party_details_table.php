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
        Schema::create('party_details', function (Blueprint $table) {
            $table->id();
            $table->string('child_name')->default('Liam');
            $table->integer('child_age')->default(5);
            $table->date('party_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue_name');
            $table->text('venue_address');
            $table->string('venue_map_url')->nullable();
            $table->text('parking_info')->nullable();
            $table->string('theme')->nullable();
            $table->text('activities')->nullable();
            $table->text('parent_contact_info');
            $table->text('gift_suggestions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('party_details');
    }
};
