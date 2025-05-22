<?php

namespace App\Notifications;

use App\Models\Dispute;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeStatusUpdatedDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Dispute $dispute;
    public User $recipient; // To customize message slightly based on who receives it

    /**
     * Create a new notification instance.
     */
    public function __construct(Dispute $dispute, User $recipient)
    {
        $this->dispute = $dispute;
        $this->recipient = $recipient;
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
        $jobTitle = $this->dispute->jobAssignment && $this->dispute->jobAssignment->job ? $this->dispute->jobAssignment->job->title : 'N/A';
        $statusText = ucfirst(str_replace('_', ' ', $this->dispute->status));
        
        $message = "The status of dispute (ID: {$this->dispute->id}) regarding job '{$jobTitle}' has been updated to '{$statusText}'.";
        if ($this->dispute->admin_remarks) {
            $message .= " Admin remarks: " . \Illuminate\Support\Str::limit($this->dispute->admin_remarks, 100);
        }

        // Link for users to view their job assignment or dashboard.
        // Admins will have their own link via admin notifications if needed, or can find it via their dashboard.
        $link = route('dashboard'); // Default
        if ($this->recipient->id === $this->dispute->reporter_id) {
            // Potentially link to where they can see the job or assignment
            if ($this->recipient->hasRole(User::ROLE_CLIENT) && $this->dispute->jobAssignment && $this->dispute->jobAssignment->job) {
                $link = route('client.jobs.show', $this->dispute->jobAssignment->job_id);
            } elseif ($this->recipient->hasRole(User::ROLE_FREELANCER) && $this->dispute->jobAssignment) {
                $link = route('freelancer.assignments.show', $this->dispute->job_assignment_id);
            }
        } elseif ($this->recipient->id === $this->dispute->reported_user_id) {
             if ($this->recipient->hasRole(User::ROLE_CLIENT) && $this->dispute->jobAssignment && $this->dispute->jobAssignment->job) {
                $link = route('client.jobs.show', $this->dispute->jobAssignment->job_id);
            } elseif ($this->recipient->hasRole(User::ROLE_FREELANCER) && $this->dispute->jobAssignment) {
                $link = route('freelancer.assignments.show', $this->dispute->job_assignment_id);
            }
        }


        return [
            'dispute_id' => $this->dispute->id,
            'job_assignment_id' => $this->dispute->job_assignment_id,
            'job_title' => $jobTitle,
            'new_status' => $this->dispute->status,
            'status_text' => $statusText,
            'admin_remarks' => $this->dispute->admin_remarks,
            'message' => $message,
            'link' => $link,
            'type' => 'dispute_status_updated',
        ];
    }
}
