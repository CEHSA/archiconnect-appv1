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
        Schema::table('assignment_tasks', function (Blueprint $table) {
            // Add status to assignment_tasks table, if it doesn't already exist from a previous migration
            // A previous migration 2025_05_18_232932_create_assignment_tasks_table.php already adds this.
            // This check is to prevent errors if this migration is run after that one,
            // or if this migration is intended to ensure the column exists with a specific default or position.
            if (!Schema::hasColumn('assignment_tasks', 'status')) {
                 $table->string('status')->default('pending')->after('description'); // e.g., pending, in_progress, completed
            }
        });

        Schema::table('job_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('job_assignments', 'progress_override_percentage')) {
                $table->unsignedTinyInteger('progress_override_percentage')->nullable()->after('admin_remarks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_tasks', 'status')) {
                // Only drop if this migration was the one to add it,
                // otherwise, this could remove a column added by another migration.
                // For simplicity here, we assume it's safe to drop if it exists.
                // A more robust check might involve checking migration batch or specific conditions.
                // $table->dropColumn('status'); // Be cautious with dropping columns in down()
            }
        });

        Schema::table('job_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('job_assignments', 'progress_override_percentage')) {
                $table->dropColumn('progress_override_percentage');
            }
        });
    }
};
