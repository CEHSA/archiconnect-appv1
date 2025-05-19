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
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete(); // User who reported the dispute
            $table->foreignId('reported_id')->constrained('users')->cascadeOnDelete(); // User who is being reported
            $table->text('reason');
            $table->string('evidence_path')->nullable(); // Path to uploaded evidence file
            $table->string('status')->default('open'); // e.g., open, under_review, resolved, closed
            $table->text('admin_remarks')->nullable();
            $table->text('client_remarks')->nullable(); // If client input is needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
