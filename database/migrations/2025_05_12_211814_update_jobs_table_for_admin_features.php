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
        Schema::table('jobs', function (Blueprint $table) {
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('budget');
            $table->decimal('not_to_exceed_budget', 10, 2)->nullable()->after('hourly_rate');
            $table->foreignId('created_by_admin_id')->nullable()->constrained('users')->onDelete('set null')->after('user_id');

            // Modify existing columns to be nullable
            // IMPORTANT: Ensure these columns exist. Based on Job model, they should.
            // If 'user_id' was created with ->constrained(), it implies foreign key.
            // We need to ensure the change to nullable doesn't break constraints if not handled.
            // For simplicity, assuming direct column modification is fine.
            // If 'user_id' is a foreign key, it might need ->nullable()->change()
            // and potentially dropping/re-adding index if issues arise.
            // For now, attempting direct change.
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->decimal('budget', 10, 2)->nullable()->change();
            $table->text('skills_required')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            // Drop foreign key and column for created_by_admin_id
            // Ensure the foreign key name matches what Laravel generates or was explicitly set
            // Default is usually tablename_columnname_foreign
            $table->dropForeign(['created_by_admin_id']);
            $table->dropColumn('created_by_admin_id');

            $table->dropColumn('hourly_rate');
            $table->dropColumn('not_to_exceed_budget');

            // Revert existing columns to non-nullable (assuming their original state)
            // This requires knowing their original state. If they were already nullable, this is incorrect.
            // Based on typical use, user_id and budget might have been non-nullable.
            // If changing nullability of indexed columns, care is needed.
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->decimal('budget', 10, 2)->nullable(false)->change();
            $table->text('skills_required')->nullable(false)->change();
        });
    }
};
