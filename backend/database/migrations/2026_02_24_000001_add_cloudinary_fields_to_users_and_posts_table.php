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
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'profile_picture_public_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('profile_picture_public_id')->nullable()->after('profile_picture');
            });
        }

        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if (!Schema::hasColumn('posts', 'title')) {
                    $table->string('title')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('posts', 'media')) {
                    $table->json('media')->nullable()->after('image_path');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'profile_picture_public_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('profile_picture_public_id');
            });
        }

        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('posts', 'title')) {
                    $columns[] = 'title';
                }
                if (Schema::hasColumn('posts', 'media')) {
                    $columns[] = 'media';
                }
                if ($columns) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
