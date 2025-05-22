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
        Schema::table('job_assignments', function (Blueprint $table) {
            // Drop the existing foreign key constraint pointing to the users table
            // SQLite might require dropping by column name array
            $table->dropForeign(['assigned_by_admin_id']);

            // Add the new foreign key constraint pointing to the admins table
            $table->foreignId('assigned_by_admin_id')->change()->constrained('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            // Drop the foreign key constraint pointing to the admins table
            // SQLite might require dropping by column name array
            $table->dropForeign(['assigned_by_admin_id']);

            // Re-add the original foreign key constraint pointing to the users table
            // Note: This assumes the column type is already correct (unsignedBigInteger)
            // Also, ensure the original foreign key name if re-adding by name, or stick to column-based for safety
            $table->foreignId('assigned_by_admin_id')->change()->constrained('users')->onDelete('cascade');
        });
    }
};
