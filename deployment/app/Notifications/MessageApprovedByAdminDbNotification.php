<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MessageApprovedByAdminDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Message $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
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
        $conversation = $this->message->conversation;
        $jobTitle = $conversation->job->title ?? 'N/A';
        $senderName = $this->message->sender->name ?? 'A user'; // Could be freelancer or client

        return [
            'title' => 'Message Approved',
            'message' => "A message from {$senderName} regarding job '{$jobTitle}' has been approved by an admin and is now visible to you.",
            'message_id' => $this->message->id,
            'conversation_id' => $conversation->id,
            'job_id' => $conversation->job_id,
            // Determine URL based on notifiable type (client or freelancer)
            'url' => $this->determineUrl($notifiable)
        ];
    }

    /**
     * Determine the appropriate URL based on the notifiable user's role.
     */
    protected function determineUrl(object $notifiable): string
    {
        // Assuming 'client' and 'freelancer' roles are stored in the User model
        // And that the conversation show page can be accessed by both with the same route name
        // but different parameters or logic within the controller.
        // For simplicity, let's assume a generic conversation view for now.
        // This might need adjustment based on actual routes.

        $conversation = $this->message->conversation;

        if ($notifiable->hasRole('client')) {
            // Example: route('client.conversations.show', $conversation->id)
            // For now, linking to the job, as conversation specific views might not exist for client yet.
            // Or, if messages are shown on job assignment page for client:
            // return route('client.jobs.show', $conversation->job_id) . '#message-' . $this->message->id;
            // Let's assume a generic message view for now, or link to the job.
            // If client views messages in context of a job:
            if ($conversation->job) {
                 return route('client.jobs.show', $conversation->job_id) . '#conversation-' . $conversation->id;
            }
        } elseif ($notifiable->hasRole('freelancer')) {
            // Freelancers view messages in context of an assignment
            $assignment = $conversation->job->assignments->where('freelancer_id', $notifiable->id)->first();
            if ($assignment) {
                return route('freelancer.assignments.show', $assignment->id) . '#conversation-' . $conversation->id;
            }
        }
        // Fallback URL if specific role route isn't found or applicable
        return route('dashboard'); // Or a generic notifications page
    }
}
