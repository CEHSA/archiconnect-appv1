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
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->comment('Sender ID')->constrained()->onDelete('cascade'); // Assumes senders are always 'users'
                $table->text('content');
                $table->boolean('is_approved')->default(true); // Consider if default should be false for admin review
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Should this be 'admins' table?
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
            });
        }
        // No 'else' block needed here if this migration is solely for creation.
        // If it was also intended to add columns to an existing 'messages' table,
        // those Schema::table calls would go in an 'else' or be separate.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To prevent accidental deletion if another migration created the table,
        // it's safer to comment this out or add specific conditions.
        // Schema::dropIfExists('messages');
    }
};
