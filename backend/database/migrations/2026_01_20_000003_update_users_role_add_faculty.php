<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the role enum to include 'faculty'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('alumni', 'mentor', 'admin', 'faculty') NOT NULL DEFAULT 'alumni'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'faculty' from role enum (if no faculty users exist)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('alumni', 'mentor', 'admin') NOT NULL DEFAULT 'alumni'");
    }
};
