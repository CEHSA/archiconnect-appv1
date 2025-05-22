<?php

namespace App\Notifications;

use App\Models\BriefingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBriefingRequestDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public BriefingRequest $briefingRequest)
    {
        //
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'briefing_request_id' => $this->briefingRequest->id,
            'client_name' => $this->briefingRequest->client->user->name,
            'project_overview' => $this->briefingRequest->project_overview,
            'url' => route('admin.briefing-requests.show', $this->briefingRequest->id),
            'message' => 'A new briefing request has been submitted by ' . $this->briefingRequest->client->user->name . '.',
        ];
    }
}
