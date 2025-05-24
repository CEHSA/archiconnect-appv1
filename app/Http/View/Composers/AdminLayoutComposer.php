<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Assuming User model is the base for Client/Freelancer
use App\Models\Admin;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log; // For debugging

class AdminLayoutComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $unreadNotificationsCount = 0;
        $user = null;

        // Check for authenticated user across different guards
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
        } elseif (Auth::guard('client')->check()) {
            $user = Auth::guard('client')->user();
        } elseif (Auth::guard('freelancer')->check()) {
            $user = Auth::guard('freelancer')->user();
        }

        if ($user) {
            // Calculate unread notifications from Laravel's built-in notifications
            $unreadNotificationsCount = $user->unreadNotifications->count();

            // Add unread message count specifically for Admin, if applicable
            if ($user instanceof Admin) {
                $adminUser = $user;
                $conversations = Conversation::whereHas('adminParticipants', function ($query) use ($adminUser) {
                    $query->where('conversation_user.user_id', $adminUser->id);
                })->get();

                foreach ($conversations as $conversation) {
                    $unreadNotificationsCount += $conversation->unreadCount($adminUser);
                }
            }
            // Add unread message count for Client and Freelancer, if applicable
            // Assuming Client and Freelancer models also have a relationship to conversations
            // and an unreadCount method, similar to Admin.
            // This part might need adjustment based on actual Client/Freelancer model structure.
            else {
                $conversations = Conversation::whereHas('participants', function ($query) use ($user) {
                    $query->where('conversation_user.user_id', $user->id);
                })->get();

                foreach ($conversations as $conversation) {
                    $unreadNotificationsCount += $conversation->unreadCount($user);
                }
            }
        }
        
        $view->with('unreadNotificationsCount', $unreadNotificationsCount);
    }
}
