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
        Schema::create('education_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->enum('level', [
                'elementary',
                'high_school',
                'senior_high',
                'college',
                'masters',
                'doctorate'
            ]);
            
            $table->string('school_name', 200);
            $table->year('year_graduated')->nullable();
            $table->text('awards')->nullable(); // Academic honors, distinctions
            $table->boolean('is_lcba')->default(false); // Mark if this is LCBA education
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_history');
    }
};
