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
        Schema::table('job_comments', function (Blueprint $table) {
            $table->string('screenshot_path')->nullable()->after('comment_text');
            $table->foreignId('work_submission_id')->nullable()->after('job_id')->constrained('work_submissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_comments', function (Blueprint $table) {
            $table->dropForeign(['work_submission_id']);
            $table->dropColumn('work_submission_id');
            $table->dropColumn('screenshot_path');
        });
    }
};
