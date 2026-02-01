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
            // LCBA Employee/Faculty badge system
            $table->boolean('is_lcba_employee_faculty')->default(false)->after('highest_educational_attainment');
            $table->string('lcba_employee_id')->nullable()->after('is_lcba_employee_faculty');
            $table->enum('lcba_verification_status', ['pending', 'verified', 'rejected'])->nullable()->after('lcba_employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_lcba_employee_faculty',
                'lcba_employee_id',
                'lcba_verification_status'
            ]);
        });
    }
};
