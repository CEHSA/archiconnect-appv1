<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Ensure Auth facade is imported

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Check for the admin guard first.
            // If the user is not authenticated with the 'admin' guard AND the current route is an admin route...
            if (!Auth::guard('admin')->check() && $request->routeIs('admin.*')) {
                return route('admin.login'); // Redirect to the admin login page.
            }
            // Default redirect for other guards (e.g., web) or if not an admin route.
            return route('login');
        }
        return null;
    }
}
