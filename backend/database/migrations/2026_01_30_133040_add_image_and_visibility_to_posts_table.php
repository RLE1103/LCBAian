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
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if (!Schema::hasColumn('posts', 'image_path')) {
                    $table->string('image_path')->nullable()->after('content');
                }
                if (!Schema::hasColumn('posts', 'visibility')) {
                    $table->enum('visibility', ['public', 'alumni_only', 'admin_only'])->default('alumni_only')->after('image_path');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('posts', 'image_path')) {
                    $columns[] = 'image_path';
                }
                if (Schema::hasColumn('posts', 'visibility')) {
                    $columns[] = 'visibility';
                }
                if ($columns) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
