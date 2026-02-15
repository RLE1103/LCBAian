<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_lcba_employee_faculty')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'is_lcba_employee_faculty')) {
                    $table->boolean('is_lcba_employee_faculty')->default(false)->after('highest_educational_attainment');
                }
            });
        }

        if (Schema::hasColumn('users', 'lcba_verification_status') && Schema::hasColumn('users', 'is_lcba_employee_faculty')) {
            DB::table('users')
                ->whereNull('lcba_verification_status')
                ->update([
                    'lcba_verification_status' => DB::raw("CASE WHEN is_lcba_employee_faculty = 1 THEN 'verified' ELSE 'rejected' END")
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_lcba_employee_faculty')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_lcba_employee_faculty');
            });
        }
    }
};
