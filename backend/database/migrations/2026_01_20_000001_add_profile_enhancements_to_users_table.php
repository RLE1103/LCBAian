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
            // Name enhancements
            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name', 50)->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'suffix')) {
                $table->string('suffix', 10)->nullable()->after('last_name');
            }
            
            // Birthday for birthday notifications and tracer study
            if (!Schema::hasColumn('users', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('email');
            }
            
            // Employment sector for Tracer Study analytics
            if (!Schema::hasColumn('users', 'employment_sector')) {
                $table->enum('employment_sector', [
                    'public_government',
                    'private',
                    'ngo_nonprofit'
                ])->nullable()->after('employment_status');
            }
            
            // Highest educational attainment
            if (!Schema::hasColumn('users', 'highest_educational_attainment')) {
                $table->enum('highest_educational_attainment', [
                    'elementary',
                    'high_school',
                    'senior_high',
                    'bachelors',
                    'masters',
                    'doctorate'
                ])->nullable()->after('batch');
            }
            
            // Privacy settings JSON
            if (!Schema::hasColumn('users', 'privacy_settings')) {
                $table->json('privacy_settings')->nullable()->after('industries_of_interest');
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
            if (Schema::hasColumn('users', 'middle_name')) {
                $columns[] = 'middle_name';
            }
            if (Schema::hasColumn('users', 'suffix')) {
                $columns[] = 'suffix';
            }
            if (Schema::hasColumn('users', 'birthdate')) {
                $columns[] = 'birthdate';
            }
            if (Schema::hasColumn('users', 'employment_sector')) {
                $columns[] = 'employment_sector';
            }
            if (Schema::hasColumn('users', 'highest_educational_attainment')) {
                $columns[] = 'highest_educational_attainment';
            }
            if (Schema::hasColumn('users', 'privacy_settings')) {
                $columns[] = 'privacy_settings';
            }
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
