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
            $table->foreignId('job_assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('current_budget', 10, 2);
            $table->decimal('requested_budget', 10, 2);
            $table->text('reason');
            $table->string('evidence_path')->nullable();
            $table->string('status')->default('pending'); // e.g., pending, approved, rejected
            $table->text('admin_remarks')->nullable();
            $table->string('client_decision')->nullable(); // e.g., pending, approved, rejected
            $table->text('client_remarks')->nullable();
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
