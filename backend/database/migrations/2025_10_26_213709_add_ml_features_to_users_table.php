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
            // Add skills as JSON array for content-based filtering
            $table->json('skills')->nullable()->after('bio');
            
            // Add cluster group for K-means clustering results
            $table->integer('cluster_group')->nullable()->after('skills');
            
            // Add career interests for better matching
            $table->json('career_interests')->nullable()->after('cluster_group');
            
            // Add current job title for clustering
            $table->string('current_job_title')->nullable()->after('career_interests');
            
            // Add industry for clustering
            $table->string('industry')->nullable()->after('current_job_title');
            
            // Add experience level
            $table->string('experience_level')->nullable()->after('industry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'skills',
                'cluster_group', 
                'career_interests',
                'current_job_title',
                'industry',
                'experience_level'
            ]);
        });
    }
};