<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id('job_id');
            $table->string('employer_name');
            $table->string('job_title');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->timestamp('posted_date')->useCurrent();
            $table->foreignId('admin_id')->nullable()->constrained('admins', 'admin_id')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};


