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
            // Increase precision for budget and not_to_exceed_budget columns
            // DECIMAL(15, 2) allows for values up to 99,999,999,999,999.99
            $table->decimal('budget', 15, 2)->nullable()->change();
            $table->decimal('not_to_exceed_budget', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            // Revert to original precision if needed, though this might cause data loss
            // if larger values were stored.
            $table->decimal('budget', 10, 2)->nullable()->change();
            $table->decimal('not_to_exceed_budget', 10, 2)->nullable()->change();
        });
    }
};
