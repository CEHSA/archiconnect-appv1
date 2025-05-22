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
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_id')->nullable()->constrained()->onDelete('cascade');
                // $table->foreignId('job_assignment_id')->nullable()->constrained()->onDelete('cascade'); // This was added in a separate migration
                $table->string('title')->nullable();
                // Other columns like participant IDs might be in the older migration or added by other migrations
                $table->timestamps();
            });
        } else {
            // Optionally, add columns here if this migration was meant to alter an existing table
            // For example, if 'title' or 'job_id' were new additions to an already existing 'conversations' table.
            // However, the original error implies this migration is trying to CREATE the table.
            // If columns are missing from the existing table that this migration expects, they should be added here.
            // Example:
            // Schema::table('conversations', function (Blueprint $table) {
            //     if (!Schema::hasColumn('conversations', 'job_id')) {
            //         $table->foreignId('job_id')->nullable()->constrained()->onDelete('cascade');
            //     }
            //     if (!Schema::hasColumn('conversations', 'title')) {
            //         $table->string('title')->nullable();
            //     }
            // });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To be safe, only drop if this migration was responsible or if it's the very last one.
        // Given the duplicate, it's complex. For now, let's assume the other migration handles the drop if needed,
        // or that a full rollback would remove this one first.
        // Schema::dropIfExists('conversations');
    }
};
