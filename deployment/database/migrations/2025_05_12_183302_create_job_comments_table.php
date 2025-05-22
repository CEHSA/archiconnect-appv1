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
        Schema::create('job_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // commenter (client)
            $table->text('comment_text');
            $table->enum('status', ['new', 'discussed', 'pending_freelancer', 'resolved'])->default('new');
            $table->foreignId('parent_comment_id')->nullable()->constrained('job_comments')->onDelete('set null');
            $table->timestamp('discussed_at')->nullable(); // When freelancer marks as discussed
            $table->timestamp('resolved_at')->nullable(); // When admin marks as resolved
            $table->timestamps();

            // Index for faster retrieval of comments by job
            $table->index(['job_id', 'created_at']);
            // Index for finding child comments
            $table->index('parent_comment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_comments');
    }
};
