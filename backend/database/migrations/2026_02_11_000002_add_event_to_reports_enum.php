<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        if (Schema::hasTable('reports')) {
            DB::statement("ALTER TABLE reports MODIFY reported_entity_type ENUM('job_post','user','post','comment','event') NOT NULL");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        if (Schema::hasTable('reports')) {
            DB::statement("ALTER TABLE reports MODIFY reported_entity_type ENUM('job_post','user','post','comment') NOT NULL");
        }
    }
};
