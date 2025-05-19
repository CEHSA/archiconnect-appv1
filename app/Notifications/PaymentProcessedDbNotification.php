<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentProcessedDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Payment $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $jobAssignment = $this->payment->jobAssignment;
        $job = $jobAssignment->job;

        return [
            'title' => 'Payment Processed',
            'message' => "A payment of {$this->payment->amount} has been processed for your work on the job: '{$job->title}'.",
            'payment_id' => $this->payment->id,
            'job_assignment_id' => $jobAssignment->id,
            'job_id' => $job->id,
            'url' => route('freelancer.assignments.show', $jobAssignment->id) // Link to the assignment details for the freelancer
        ];
    }
}
