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
            $table->unsignedBigInteger('assigned_freelancer_id')->nullable()->after('user_id');

            $table->foreign('assigned_freelancer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // Or 'cascade' or 'restrict' depending on desired behavior
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropForeign(['assigned_freelancer_id']);
            $table->dropColumn('assigned_freelancer_id');
        });
    }
};
