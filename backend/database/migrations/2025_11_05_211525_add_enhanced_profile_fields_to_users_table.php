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
            // Contact & Socials
            $table->string('public_email')->nullable()->after('email');
            $table->string('linkedin_url')->nullable()->after('public_email');
            $table->string('portfolio_url')->nullable()->after('linkedin_url');
            
            // Location (split from single location field)
            // Add city and country after experience_level
            $table->string('city')->nullable()->after('experience_level');
            $table->string('country')->nullable()->after('city');
            
            // Career fields
            $table->enum('employment_status', [
                'employed_full_time',
                'employed_part_time',
                'self_employed',
                'in_study',
                'unemployed_looking',
                'unemployed_not_looking'
            ])->nullable()->after('experience_level');
            
            $table->enum('years_of_experience', [
                '0-2',
                '3-5',
                '6-10',
                '10+'
            ])->nullable()->after('employment_status');
            
            $table->enum('salary_range', [
                'prefer_not_to_say',
                '20000-39999',
                '40000-59999',
                '60000-79999',
                '80000-99999',
                '100000+'
            ])->nullable()->after('years_of_experience');
            
            // Career Preferences
            $table->json('work_setup_preferences')->nullable()->after('salary_range'); // ['on_site', 'hybrid', 'remote']
            $table->json('employment_type_preferences')->nullable()->after('work_setup_preferences'); // ['full_time', 'part_time', 'contract', 'internship']
            $table->json('industries_of_interest')->nullable()->after('employment_type_preferences'); // Array of industry names
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'public_email',
                'linkedin_url',
                'portfolio_url',
                'city',
                'country',
                'employment_status',
                'years_of_experience',
                'salary_range',
                'work_setup_preferences',
                'employment_type_preferences',
                'industries_of_interest'
            ]);
        });
    }
};
