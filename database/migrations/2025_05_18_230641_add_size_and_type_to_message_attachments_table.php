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
        Schema::table('message_attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('file_size')->nullable()->after('original_file_name');
            $table->string('file_type')->nullable()->after('file_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_attachments', function (Blueprint $table) {
            $table->dropColumn(['file_size', 'file_type']);
        });
    }
};
