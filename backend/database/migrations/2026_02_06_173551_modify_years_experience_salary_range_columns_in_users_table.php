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
            // Change years_of_experience from ENUM to integer (0-100)
            $table->integer('years_of_experience')->nullable()->change();
            
            // Change salary_range from ENUM to string (freeform text like "20,000 - 25,000")
            $table->string('salary_range', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert to ENUM
            $table->enum('years_of_experience', [
                '0-2',
                '3-5',
                '6-10',
                '10+'
            ])->nullable()->change();
            
            $table->enum('salary_range', [
                'prefer_not_to_say',
                '20000-39999',
                '40000-59999',
                '60000-79999',
                '80000-99999',
                '100000+'
            ])->nullable()->change();
        });
    }
};
