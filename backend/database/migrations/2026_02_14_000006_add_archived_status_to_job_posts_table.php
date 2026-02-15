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

        DB::statement("ALTER TABLE `job_posts` MODIFY `status` ENUM('pending','approved','rejected','flagged','archived') NOT NULL DEFAULT 'approved'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE `job_posts` MODIFY `status` ENUM('pending','approved','rejected','flagged') NOT NULL DEFAULT 'approved'");
    }
};
