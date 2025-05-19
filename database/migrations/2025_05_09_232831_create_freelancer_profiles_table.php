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
        Schema::create('freelancer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('skills')->nullable();
            $table->string('portfolio_link')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->string('availability')->nullable();
            $table->string('experience_level')->nullable();
            $table->boolean('receive_new_job_notifications')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancer_profiles');
    }
};
