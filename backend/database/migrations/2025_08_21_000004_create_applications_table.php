<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id('application_id');
            $table->foreignId('alumni_id')->constrained('alumni', 'alumni_id')->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('job_postings', 'job_id')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('date_applied')->useCurrent();
            $table->timestamps();
            $table->unique(['alumni_id', 'job_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};


