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
        Schema::table('budget_appeals', function (Blueprint $table) {
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by_client_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budget_appeals', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by_admin_id']);
            $table->dropForeign(['reviewed_by_client_id']);
            $table->dropColumn(['reviewed_by_admin_id', 'reviewed_by_client_id', 'reviewed_at']);
        });
    }
};
