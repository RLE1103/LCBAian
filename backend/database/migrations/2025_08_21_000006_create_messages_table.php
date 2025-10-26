<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->foreignId('sender_id')->constrained('alumni', 'alumni_id')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('alumni', 'alumni_id')->cascadeOnDelete();
            $table->text('content');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
            $table->index(['sender_id', 'receiver_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};


