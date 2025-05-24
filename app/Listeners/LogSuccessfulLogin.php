<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\UserActivity;
use Illuminate\Http\Request; // Import Request to get IP and User Agent
use Illuminate\Support\Facades\Session; // Import Session to get session ID

class LogSuccessfulLogin
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
    public function handle(Login $event): void
    {
        UserActivity::create([
            'user_id' => $event->user->id,
            'activity_type' => 'login',
            'details' => [
                'guard' => $event->guard,
            ],
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->header('User-Agent'),
            'session_id' => Session::getId(),
        ]);
    }
}
