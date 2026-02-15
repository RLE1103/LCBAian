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
            if (!Schema::hasColumn('users', 'skills')) {
                $table->json('skills')->nullable()->after('bio');
            }

            if (!Schema::hasColumn('users', 'cluster_group')) {
                $table->integer('cluster_group')->nullable()->after('skills');
            }

            if (!Schema::hasColumn('users', 'career_interests')) {
                $table->json('career_interests')->nullable()->after('cluster_group');
            }

            if (!Schema::hasColumn('users', 'current_job_title')) {
                $table->string('current_job_title')->nullable()->after('career_interests');
            }

            if (!Schema::hasColumn('users', 'industry')) {
                $table->string('industry')->nullable()->after('current_job_title');
            }

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
            $columns = [];
            if (Schema::hasColumn('users', 'skills')) {
                $columns[] = 'skills';
            }
            if (Schema::hasColumn('users', 'cluster_group')) {
                $columns[] = 'cluster_group';
            }
            if (Schema::hasColumn('users', 'career_interests')) {
                $columns[] = 'career_interests';
            }
            if (Schema::hasColumn('users', 'current_job_title')) {
                $columns[] = 'current_job_title';
            }
            if (Schema::hasColumn('users', 'industry')) {
                $columns[] = 'industry';
            }
            if (Schema::hasColumn('users', 'experience_level')) {
                $columns[] = 'experience_level';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
