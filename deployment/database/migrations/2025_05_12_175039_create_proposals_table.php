<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('proposed_budget', 10, 2);
            $table->text('cover_letter');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('admin_remarks')->nullable();
            $table->timestamps();

            // Ensure a freelancer can only submit one proposal per job
            $table->unique(['job_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
