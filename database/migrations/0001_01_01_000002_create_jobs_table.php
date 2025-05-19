<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration now defines the application's main 'jobs' table.
     * The original content defining Laravel's queue tables has been removed.
     * If Laravel's database queue is needed, a separate standard migration should be used.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Client who posted the job
            $table->string('title');
            $table->text('description');
            $table->decimal('budget', 10, 2); // Will be made nullable in a later migration
            $table->text('skills_required'); // Will be made nullable in a later migration
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
