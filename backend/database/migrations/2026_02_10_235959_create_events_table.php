<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('title', 100);
                $table->text('description');
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->string('location', 255);
                $table->string('type', 50)->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->index('start_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
