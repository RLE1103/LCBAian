<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_warnings')) {
            Schema::create('user_warnings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('issued_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('report_id')->nullable()->constrained('reports')->nullOnDelete();
                $table->text('warning_message');
                $table->boolean('is_acknowledged')->default(false);
                $table->timestamps();
                $table->index(['user_id', 'is_acknowledged']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_warnings')) {
            Schema::dropIfExists('user_warnings');
        }
    }
};
