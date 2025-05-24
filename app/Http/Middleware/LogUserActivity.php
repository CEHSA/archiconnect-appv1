<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = Session::getId();

            // Log page views
            if ($request->isMethod('GET')) {
                UserActivity::create([
                    'user_id' => $user->id,
                    'activity_type' => 'view_page',
                    'details' => [
                        'url' => $request->fullUrl(),
                        'path' => $request->path(),
                    ],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'session_id' => $sessionId,
                ]);
            }

            // You can add more specific logging for POST/PUT/DELETE actions here
            // For example, if you want to log a 'create_job' action:
            // if ($request->isMethod('POST') && $request->routeIs('admin.jobs.store')) {
            //     UserActivity::create([
            //         'user_id' => $user->id,
            //         'activity_type' => 'create_job',
            //         'details' => [
            //             'job_title' => $request->input('title'),
            //         ],
            //         'ip_address' => $request->ip(),
            //         'user_agent' => $request->header('User-Agent'),
            //         'session_id' => $sessionId,
            //     ]);
            // }
        }

        return $next($request);
    }
}
