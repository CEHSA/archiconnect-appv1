<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TimeLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Notifications\TimerAutoStoppedNotification; // This is the Mailable
use App\Notifications\TimerAutoStoppedDbNotification; // Add this line for DB notification
use Illuminate\Support\Facades\Notification as LaravelNotification; // Add this line and alias

class AutoStopTimers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timelogs:auto-stop {--duration=4 : Maximum duration in hours before auto-stopping.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically stops timers that have been running for too long and marks them as auto-stopped.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $maxDurationHours = (int) $this->option('duration');
        if ($maxDurationHours <= 0) {
            $this->error('Invalid duration. Please provide a positive number of hours.');
            return 1;
        }

        $this->info("Starting auto-stop process for timers running longer than {$maxDurationHours} hours...");
        Log::info("AutoStopTimers: Starting process for timers > {$maxDurationHours} hours.");

        $autoStopThreshold = Carbon::now()->subHours($maxDurationHours);
        $stoppedCount = 0;

        // Fetch active time logs that started before the threshold
        $activeTimersToStop = TimeLog::whereNull('end_time')
            ->where('start_time', '<=', $autoStopThreshold)
            ->with('freelancer') // Eager load freelancer for notification
            ->get();

        if ($activeTimersToStop->isEmpty()) {
            $this->info('No timers found exceeding the maximum duration.');
            Log::info('AutoStopTimers: No timers to auto-stop.');
            return 0;
        }

        foreach ($activeTimersToStop as $timer) {
            $startTime = Carbon::parse($timer->start_time);
            $timer->end_time = $startTime->copy()->addHours($maxDurationHours); // Stop exactly at max duration from start
            $timer->duration = $timer->end_time->diffInSeconds($startTime);
            $timer->is_auto_stopped = true;
            $timer->save();

            $stoppedCount++;
            $this->line("Auto-stopped timer ID: {$timer->id} for freelancer ID: {$timer->freelancer_id}. Started: {$startTime}, Auto-stopped at: {$timer->end_time}");
            Log::info("AutoStopTimers: Auto-stopped timer ID {$timer->id} for freelancer {$timer->freelancer_id}.");

            // Notify the freelancer
            if ($timer->freelancer) {
                try {
                    // Send Email Notification
                    $timer->freelancer->notify(new TimerAutoStoppedNotification($timer)); // This is the Mailable
                    Log::info("AutoStopTimers: Email notification sent to freelancer {$timer->freelancer_id} for timer {$timer->id}.");

                    // Send Database Notification
                    LaravelNotification::send($timer->freelancer, new TimerAutoStoppedDbNotification($timer));
                    Log::info("AutoStopTimers: Database notification sent to freelancer {$timer->freelancer_id} for timer {$timer->id}.");

                } catch (\Exception $e) {
                    Log::error("AutoStopTimers: Failed to send notification(s) for timer ID {$timer->id} to freelancer {$timer->freelancer_id}. Error: " . $e->getMessage());
                    $this->error("Failed to send notification(s) for timer ID {$timer->id}. Error: " . $e->getMessage());
                }
            } else {
                Log::warning("AutoStopTimers: Freelancer not found for timer ID {$timer->id}. Cannot send notification(s).");
            }
        }

        $this->info("Successfully auto-stopped {$stoppedCount} timers.");
        Log::info("AutoStopTimers: Process completed. Auto-stopped {$stoppedCount} timers.");
        return 0;
    }
}
