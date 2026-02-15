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
        if (!Schema::hasTable('users')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        // Since columns are already changed to integer/string types,
        // any invalid values will cause errors. Set them to NULL to clean up.
        // This migration should run AFTER the column type change migration.
        
        // For years_of_experience: Set any non-numeric values to NULL
        // (Using raw SQL to avoid type conversion issues)
        DB::statement("UPDATE users SET years_of_experience = NULL WHERE years_of_experience IS NOT NULL AND years_of_experience NOT REGEXP '^[0-9]+$'");
        
        // For salary_range: Clean up old ENUM values if they exist
        // (These should work since salary_range is a string column)
        try {
            DB::table('users')->where('salary_range', 'prefer_not_to_say')->update(['salary_range' => null]);
            DB::table('users')->where('salary_range', '20000-39999')->update(['salary_range' => '20,000 - 39,999']);
            DB::table('users')->where('salary_range', '40000-59999')->update(['salary_range' => '40,000 - 59,999']);
            DB::table('users')->where('salary_range', '60000-79999')->update(['salary_range' => '60,000 - 79,999']);
            DB::table('users')->where('salary_range', '80000-99999')->update(['salary_range' => '80,000 - 99,999']);
            DB::table('users')->where('salary_range', '100000+')->update(['salary_range' => '100,000+']);
        } catch (\Exception $e) {
            // If there's an error, it likely means the data is already clean
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert - data transformation is one-way
    }
};
