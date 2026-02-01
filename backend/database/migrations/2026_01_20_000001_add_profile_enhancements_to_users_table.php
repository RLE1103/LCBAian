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
            // Name enhancements
            $table->string('middle_name', 50)->nullable()->after('first_name');
            $table->string('suffix', 10)->nullable()->after('last_name'); // Jr., Sr., III, etc.
            
            // Birthday for birthday notifications and tracer study
            $table->date('birthdate')->nullable()->after('email');
            
            // Employment sector for Tracer Study analytics
            $table->enum('employment_sector', [
                'public_government',
                'private',
                'ngo_nonprofit'
            ])->nullable()->after('employment_status');
            
            // Highest educational attainment
            $table->enum('highest_educational_attainment', [
                'elementary',
                'high_school',
                'senior_high',
                'bachelors',
                'masters',
                'doctorate'
            ])->nullable()->after('batch');
            
            // Privacy settings JSON
            $table->json('privacy_settings')->nullable()->after('industries_of_interest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name',
                'suffix',
                'birthdate',
                'employment_sector',
                'highest_educational_attainment',
                'privacy_settings'
            ]);
        });
    }
};
