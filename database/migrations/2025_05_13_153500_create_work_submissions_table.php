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
        Schema::create('work_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_assignment_id')->constrained('job_assignments')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade'); // The user who submitted
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // Admin who reviewed/handled

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('file_path')->nullable(); // Path to the uploaded file in storage
            $table->string('original_filename')->nullable(); // Original name of the uploaded file
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('size')->nullable(); // File size in bytes

            $table->string('status')->default('submitted'); // e.g., submitted, under_review, needs_revision, approved_by_admin, cancelled
            
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_remarks')->nullable(); // Remarks from admin after review

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_submissions');
    }
};
