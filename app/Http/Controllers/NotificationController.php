<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications;
        $readNotifications = $user->readNotifications;

        return view('notifications.index', compact('notifications', 'readNotifications'));
    }

    public function markAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return redirect()->back()->with('status', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return redirect()->back()->with('status', 'All notifications marked as read.');
    }
}
