<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("
                UPDATE job_posts
                SET experience_level = CASE
                    WHEN experience_level IS NULL OR experience_level = '' THEN NULL
                    WHEN experience_level REGEXP '^[0-9]+$' THEN experience_level
                    WHEN LOWER(experience_level) LIKE '%0-1%' THEN 0
                    WHEN LOWER(experience_level) LIKE '%1-3%' THEN 1
                    WHEN LOWER(experience_level) LIKE '%3-5%' THEN 3
                    WHEN LOWER(experience_level) LIKE '%5-10%' THEN 5
                    WHEN LOWER(experience_level) LIKE '%10+%' THEN 10
                    WHEN LOWER(experience_level) LIKE '%entry%' THEN 0
                    WHEN LOWER(experience_level) LIKE '%junior%' THEN 1
                    WHEN LOWER(experience_level) LIKE '%mid%' THEN 3
                    WHEN LOWER(experience_level) LIKE '%senior%' THEN 5
                    WHEN LOWER(experience_level) LIKE '%lead%' THEN 10
                    WHEN LOWER(experience_level) LIKE '%expert%' THEN 10
                    ELSE NULL
                END
            ");
            DB::statement('ALTER TABLE job_posts MODIFY experience_level INT NULL');
            return;
        }
        if ($driver === 'pgsql') {
            DB::statement("
                UPDATE job_posts
                SET experience_level = CASE
                    WHEN experience_level IS NULL OR experience_level = '' THEN NULL
                    WHEN experience_level ~ '^[0-9]+$' THEN experience_level
                    WHEN LOWER(experience_level) LIKE '%0-1%' THEN '0'
                    WHEN LOWER(experience_level) LIKE '%1-3%' THEN '1'
                    WHEN LOWER(experience_level) LIKE '%3-5%' THEN '3'
                    WHEN LOWER(experience_level) LIKE '%5-10%' THEN '5'
                    WHEN LOWER(experience_level) LIKE '%10+%' THEN '10'
                    WHEN LOWER(experience_level) LIKE '%entry%' THEN '0'
                    WHEN LOWER(experience_level) LIKE '%junior%' THEN '1'
                    WHEN LOWER(experience_level) LIKE '%mid%' THEN '3'
                    WHEN LOWER(experience_level) LIKE '%senior%' THEN '5'
                    WHEN LOWER(experience_level) LIKE '%lead%' THEN '10'
                    WHEN LOWER(experience_level) LIKE '%expert%' THEN '10'
                    ELSE NULL
                END
            ");
            DB::statement('ALTER TABLE job_posts ALTER COLUMN experience_level TYPE INTEGER USING experience_level::integer');
            return;
        }
        if ($driver === 'sqlite') {
            DB::statement('ALTER TABLE job_posts ADD COLUMN experience_level_int INTEGER');
            DB::statement("
                UPDATE job_posts
                SET experience_level_int = CASE
                    WHEN experience_level IS NULL OR experience_level = '' THEN NULL
                    WHEN experience_level GLOB '[0-9]*' THEN CAST(experience_level AS INTEGER)
                    WHEN LOWER(experience_level) LIKE '%0-1%' THEN 0
                    WHEN LOWER(experience_level) LIKE '%1-3%' THEN 1
                    WHEN LOWER(experience_level) LIKE '%3-5%' THEN 3
                    WHEN LOWER(experience_level) LIKE '%5-10%' THEN 5
                    WHEN LOWER(experience_level) LIKE '%10+%' THEN 10
                    WHEN LOWER(experience_level) LIKE '%entry%' THEN 0
                    WHEN LOWER(experience_level) LIKE '%junior%' THEN 1
                    WHEN LOWER(experience_level) LIKE '%mid%' THEN 3
                    WHEN LOWER(experience_level) LIKE '%senior%' THEN 5
                    WHEN LOWER(experience_level) LIKE '%lead%' THEN 10
                    WHEN LOWER(experience_level) LIKE '%expert%' THEN 10
                    ELSE NULL
                END
            ");
            DB::statement('ALTER TABLE job_posts DROP COLUMN experience_level');
            DB::statement('ALTER TABLE job_posts RENAME COLUMN experience_level_int TO experience_level');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE job_posts MODIFY experience_level VARCHAR(50) NULL');
            return;
        }
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_posts ALTER COLUMN experience_level TYPE VARCHAR(50)');
            return;
        }
        if ($driver === 'sqlite') {
            DB::statement('ALTER TABLE job_posts ADD COLUMN experience_level_text VARCHAR(50)');
            DB::statement('UPDATE job_posts SET experience_level_text = CAST(experience_level AS TEXT)');
            DB::statement('ALTER TABLE job_posts DROP COLUMN experience_level');
            DB::statement('ALTER TABLE job_posts RENAME COLUMN experience_level_text TO experience_level');
        }
    }
};
