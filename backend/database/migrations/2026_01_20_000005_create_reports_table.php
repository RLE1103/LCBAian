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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            
            // Reporter information
            $table->foreignId('reporter_user_id')->constrained('users')->onDelete('cascade');
            
            // What is being reported
            $table->enum('reported_entity_type', [
                'job_post',
                'user',
                'post',
                'comment'
            ]);
            $table->unsignedBigInteger('reported_entity_id');
            
            // Report details
            $table->enum('reason', [
                'spam',
                'inappropriate_content',
                'scam_fraud',
                'harassment',
                'false_information',
                'other'
            ]);
            $table->text('description')->nullable();
            
            // Status and review
            $table->enum('status', [
                'pending',
                'under_review',
                'resolved',
                'dismissed'
            ])->default('pending');
            
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('reporter_user_id');
            $table->index(['reported_entity_type', 'reported_entity_id']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
