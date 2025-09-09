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
        Schema::table('guests', function (Blueprint $table) {
            // Dutch versions of existing fields
            $table->string('parent_name_nl')->nullable()->after('parent_name');
            $table->string('parent_email_nl')->nullable()->after('parent_email');
            $table->string('parent_phone_nl')->nullable()->after('parent_phone');
            
            // Preferred language
            $table->enum('preferred_language', ['en', 'nl'])->default('en')->after('friendship_photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn([
                'parent_name_nl',
                'parent_email_nl', 
                'parent_phone_nl',
                'preferred_language'
            ]);
        });
    }
};
