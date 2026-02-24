<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'link')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('link', 2048)->nullable()->after('location');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'link')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('link');
            });
        }
    }
};
