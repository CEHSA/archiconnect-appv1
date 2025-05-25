<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log; // Added for logging

class LastUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */ // Added type hint
            $user = Auth::user();
            $oldLastActivity = $user->last_activity_at;
            $newLastActivity = Carbon::now();

            Log::info("LastUserActivity Middleware: User ID {$user->id} - Old activity: {$oldLastActivity}, New activity: {$newLastActivity}");

            $user->last_activity_at = $newLastActivity;
            if ($user->isDirty('last_activity_at')) { // Check if the attribute has actually changed
                $user->save();
                Log::info("LastUserActivity Middleware: User ID {$user->id} - last_activity_at updated and saved.");
            } else {
                Log::info("LastUserActivity Middleware: User ID {$user->id} - last_activity_at not dirty, no save needed.");
            }
        } else {
            Log::info("LastUserActivity Middleware: No authenticated user.");
        }

        return $next($request);
    }
}
