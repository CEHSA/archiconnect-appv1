<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Job;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel for job-specific comments
Broadcast::channel('job.{jobId}.comments', function (User $user, int $jobId) {
    $job = Job::find($jobId);
    if (!$job) {
        return false;
    }
    // Allow job owner (client), assigned freelancer, or admin
    return $user->id === $job->client_id || 
           ($job->freelancer_id && $user->id === $job->freelancer_id) ||
           $user->hasRole(User::ROLE_ADMIN);
});

// Channel for admin-specific comment notifications
Broadcast::channel('admin.comments', function (User $user) {
    return $user->hasRole(User::ROLE_ADMIN);
});

// Channel for user-specific notifications (e.g., new messages, proposal updates)
Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return (int) $user->id === (int) $userId;
});

// Channel for conversation messages
Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    $conversation = \App\Models\Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }
    return $conversation->isParticipant($user);
});
