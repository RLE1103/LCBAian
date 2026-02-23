<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('education_history', function (Blueprint $table) {
            $table->string('degree', 200)->nullable()->after('school_name');
            $table->string('field_of_study', 200)->nullable()->after('degree');
            $table->string('program', 200)->nullable()->after('field_of_study');
        });
    }

    public function down(): void
    {
        Schema::table('education_history', function (Blueprint $table) {
            $table->dropColumn(['degree', 'field_of_study', 'program']);
        });
    }
};
