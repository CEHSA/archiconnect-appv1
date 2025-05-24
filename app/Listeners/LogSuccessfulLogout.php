<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class LogSuccessfulLogout
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create the event listener.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $user = $event->user;
        $sessionId = Session::getId();

        // Find the corresponding login activity for this session
        $loginActivity = UserActivity::where('user_id', $user->id)
                                     ->where('activity_type', 'login')
                                     ->where('session_id', $sessionId)
                                     ->latest()
                                     ->first();

        $durationMinutes = null;
        if ($loginActivity) {
            $loginTime = Carbon::parse($loginActivity->created_at);
            $logoutTime = Carbon::now();
            $durationMinutes = $logoutTime->diffInMinutes($loginTime);

            // Update the login activity with duration if needed, or just use it for calculation
            // For simplicity, we'll just calculate and log with the logout event
        }

        UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'logout',
            'details' => [
                'guard' => $event->guard,
            ],
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->header('User-Agent'),
            'session_id' => $sessionId,
            'duration_minutes' => $durationMinutes,
        ]);
    }
}
