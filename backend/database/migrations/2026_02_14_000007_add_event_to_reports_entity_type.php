<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE `reports` MODIFY `reported_entity_type` ENUM('job_post','user','post','comment','event') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE `reports` MODIFY `reported_entity_type` ENUM('job_post','user','post','comment') NOT NULL");
    }
};
