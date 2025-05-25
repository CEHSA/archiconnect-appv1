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
            $table->foreignId('job_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title')->nullable(); // Title for the conversation, e.g., Job Title
            $table->timestamp('last_message_at')->nullable(); // To sort conversations by recent activity
            // 'job_assignment_id' will be added by a later migration
            // 'status' might be useful but was not in the newer create migration.
            $table->timestamps();
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
