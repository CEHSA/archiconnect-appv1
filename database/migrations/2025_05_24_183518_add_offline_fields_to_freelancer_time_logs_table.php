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
        Schema::table('freelancer_time_logs', function (Blueprint $table) {
            $table->boolean('is_offline_recorded')->default(false)->after('status');
            $table->string('offline_id')->nullable()->after('is_offline_recorded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freelancer_time_logs', function (Blueprint $table) {
            $table->dropColumn('is_offline_recorded');
            $table->dropColumn('offline_id');
        });
    }
};
