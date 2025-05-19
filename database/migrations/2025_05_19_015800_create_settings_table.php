<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting; // For seeding default values

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // E.g., string, boolean, integer, text
            $table->string('group')->default('general'); // For grouping settings in UI
            $table->string('label'); // User-friendly label for the setting
            $table->text('description')->nullable(); // Optional description
            $table->timestamps();
        });

        // Seed default settings
        Setting::create([
            'key' => 'time_tracking_auto_stop_hours',
            'value' => '4',
            'type' => 'integer',
            'group' => 'workflow',
            'label' => 'Time Tracking Auto-Stop (Hours)',
            'description' => 'Automatically stop freelancer timers after this many continuous hours. Set to 0 to disable.'
        ]);

        Setting::create([
            'key' => 'maintenance_mode',
            'value' => '0', // 0 for false, 1 for true
            'type' => 'boolean',
            'group' => 'system',
            'label' => 'Maintenance Mode',
            'description' => 'Puts the site into maintenance mode, making it inaccessible to non-admins.'
        ]);

        Setting::create([
            'key' => 'admin_notification_email',
            'value' => 'admin@example.com',
            'type' => 'string',
            'group' => 'notifications',
            'label' => 'Admin Notification Email',
            'description' => 'Primary email address for critical system notifications.'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
