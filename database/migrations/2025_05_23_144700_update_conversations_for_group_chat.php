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
        // Update conversations table
        Schema::table('conversations', function (Blueprint $table) {
            // Drop old participant columns if they exist
            if (Schema::hasColumn('conversations', 'participant1_id')) {
                $table->dropForeign(['participant1_id']);
                $table->dropColumn(['participant1_id', 'participant1_type']);
            }
            if (Schema::hasColumn('conversations', 'participant2_id')) {
                $table->dropForeign(['participant2_id']);
                $table->dropColumn(['participant2_id', 'participant2_type']);
            }
            
            // Add new columns if they don't exist
            if (!Schema::hasColumn('conversations', 'status')) {
                $table->string('status')->default('open')->after('title');
            }
            if (!Schema::hasColumn('conversations', 'created_by_user_id')) {
                $table->foreignId('created_by_user_id')->nullable()->after('job_id')->constrained('users')->onDelete('set null');
            }
        });

        // Create conversation_user pivot table if it doesn't exist
        if (!Schema::hasTable('conversation_user')) {
            Schema::create('conversation_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamp('last_read_at')->nullable();
                $table->timestamps();

                $table->unique(['conversation_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_user');

        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('conversations', 'created_by_user_id')) {
                $table->dropForeign(['created_by_user_id']);
                $table->dropColumn('created_by_user_id');
            }
        });
    }
};
