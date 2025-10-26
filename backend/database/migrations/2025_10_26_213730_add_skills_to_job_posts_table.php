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
        Schema::table('job_posts', function (Blueprint $table) {
            // Add required skills as JSON array for content-based filtering
            $table->json('required_skills')->nullable()->after('description');
            
            // Add preferred skills
            $table->json('preferred_skills')->nullable()->after('required_skills');
            
            // Add industry for clustering
            $table->string('industry')->nullable()->after('preferred_skills');
            
            // Add experience level required
            $table->string('experience_level')->nullable()->after('industry');
            
            // Add salary range for better matching
            $table->string('salary_range')->nullable()->after('experience_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropColumn([
                'required_skills',
                'preferred_skills',
                'industry',
                'experience_level',
                'salary_range'
            ]);
        });
    }
};