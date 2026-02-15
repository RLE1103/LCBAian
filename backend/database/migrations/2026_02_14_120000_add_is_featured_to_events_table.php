<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'is_featured')) {
            Schema::table('events', function (Blueprint $table) {
                $table->boolean('is_featured')->default(false)->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'is_featured')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('is_featured');
            });
        }
    }
};
