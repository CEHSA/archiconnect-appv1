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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant1_id')->constrained('users')->onDelete('cascade');
            $table->string('participant1_type')->default('user'); // e.g., 'user', 'admin'
            $table->foreignId('participant2_id')->constrained('users')->onDelete('cascade');
            $table->string('participant2_type')->default('user'); // e.g., 'user', 'admin'
            $table->foreignId('job_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // Ensure uniqueness of conversation between participants for a job
            $table->unique(['participant1_id', 'participant1_type', 'participant2_id', 'participant2_type', 'job_id'], 'unique_conversation_participants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
