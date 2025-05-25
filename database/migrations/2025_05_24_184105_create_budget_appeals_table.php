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
        Schema::create('budget_appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_assignment_id')->constrained('job_assignments')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('current_budget', 10, 2); // From model
            $table->decimal('requested_budget', 10, 2); // From model
            $table->text('reason');
            $table->string('evidence_path')->nullable(); // From model
            $table->string('status')->default('pending'); // e.g., 'pending', 'approved', 'rejected'
            $table->text('admin_remarks')->nullable(); // From model
            $table->string('client_decision')->nullable(); // From model (e.g., 'approved', 'rejected')
            $table->text('client_remarks')->nullable(); // From model
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by_client_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_appeals');
    }
};
