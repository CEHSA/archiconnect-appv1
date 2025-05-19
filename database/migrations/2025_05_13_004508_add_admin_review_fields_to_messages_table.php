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
        Schema::table('messages', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('read_at');
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->after('status');
            $table->text('admin_remarks')->nullable()->after('reviewed_by_admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by_admin_id']);
            $table->dropColumn(['status', 'reviewed_by_admin_id', 'admin_remarks']);
        });
    }
};
