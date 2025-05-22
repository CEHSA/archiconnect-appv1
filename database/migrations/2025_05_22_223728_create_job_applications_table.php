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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade'); // Assuming freelancers are in users table
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->text('cover_letter')->nullable();
            $table->decimal('proposed_rate', 8, 2)->nullable();
            $table->string('estimated_timeline')->nullable();
            $table->string('status')->default('submitted'); // e.g., submitted, viewed, shortlisted, rejected, accepted
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            // Optional: Add a unique constraint if a freelancer can only apply once to a specific job posting
            // $table->unique(['job_posting_id', 'freelancer_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
