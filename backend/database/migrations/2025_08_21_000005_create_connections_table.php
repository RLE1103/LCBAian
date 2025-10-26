<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->id('connection_id');
            $table->foreignId('alumni_id_from')->constrained('alumni', 'alumni_id')->cascadeOnDelete();
            $table->foreignId('alumni_id_to')->constrained('alumni', 'alumni_id')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted'])->default('pending');
            $table->timestamps();
            $table->unique(['alumni_id_from', 'alumni_id_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};


