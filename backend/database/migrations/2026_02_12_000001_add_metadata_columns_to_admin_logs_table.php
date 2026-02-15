<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_logs')) {
            return;
        }

        Schema::table('admin_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_logs', 'model_type')) {
                $table->string('model_type')->nullable()->after('action');
            }
            if (!Schema::hasColumn('admin_logs', 'model_id')) {
                $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            }
            if (!Schema::hasColumn('admin_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('model_id');
            }
            if (!Schema::hasColumn('admin_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('admin_logs', 'details')) {
                $table->json('details')->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('admin_logs', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('admin_logs', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('admin_logs')) {
            return;
        }

        Schema::table('admin_logs', function (Blueprint $table) {
            $columns = ['model_type', 'model_id', 'ip_address', 'user_agent', 'details', 'updated_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('admin_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
