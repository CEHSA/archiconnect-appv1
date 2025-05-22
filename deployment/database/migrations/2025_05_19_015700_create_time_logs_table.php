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
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_task_id')->constrained('assignment_tasks')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade'); // Assuming freelancers are in 'users' table
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->integer('duration_seconds')->nullable(); // Duration in seconds
            $table->text('freelancer_comments')->nullable();
            $table->string('proof_of_work_path')->nullable();
            $table->string('proof_of_work_filename')->nullable();
            $table->enum('status', ['running', 'pending_review', 'approved', 'declined'])->default('running');
            $table->text('admin_comments')->nullable();
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
