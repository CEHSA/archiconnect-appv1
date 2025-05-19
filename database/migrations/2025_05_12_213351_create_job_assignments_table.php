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
        Schema::create('job_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade'); // Assuming freelancer is a user
            $table->foreignId('assigned_by_admin_id')->constrained('users')->onDelete('cascade'); // Admin who assigned
            $table->string('status')->default('pending_freelancer_acceptance'); // e.g., pending_freelancer_acceptance, accepted, declined, assigned, in_progress, completed
            $table->text('freelancer_remarks')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_assignments');
    }
};
