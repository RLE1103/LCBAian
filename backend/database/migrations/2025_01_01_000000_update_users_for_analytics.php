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
            // Add cluster_group column if it doesn't exist
            if (!Schema::hasColumn('users', 'cluster_group')) {
                $table->integer('cluster_group')->nullable()->after('skills');
            }
            
            // Add industry if it doesn't exist
            if (!Schema::hasColumn('users', 'industry')) {
                $table->string('industry')->nullable()->after('current_job_title');
            }
            
            // Add experience_level if it doesn't exist
            if (!Schema::hasColumn('users', 'experience_level')) {
                $table->string('experience_level')->nullable()->after('industry');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cluster_group');
            $table->dropColumn('industry');
            $table->dropColumn('experience_level');
        });
    }
};
