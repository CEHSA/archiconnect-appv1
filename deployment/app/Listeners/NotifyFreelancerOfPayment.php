<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\PaymentProcessed;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentProcessedNotification;
use App\Notifications\PaymentProcessedDbNotification; // Add this line
use Illuminate\Support\Facades\Notification; // Add this line

class NotifyFreelancerOfPayment implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentProcessed $event): void // Type-hint the event
    {
        $payment = $event->payment;
        $jobAssignment = $payment->jobAssignment; // Assuming Payment model has a jobAssignment relationship
        $freelancer = $jobAssignment->freelancer; // Assuming JobAssignment model has a freelancer relationship

        // Send notification to freelancer
        if ($freelancer && $freelancer->user) { // Ensure freelancer and freelancer->user exist
            // Send Email Notification
            Mail::to($freelancer->user->email)->send(new PaymentProcessedNotification($payment));

            // Send Database Notification
            Notification::send($freelancer->user, new PaymentProcessedDbNotification($payment));
        }
    }
}
