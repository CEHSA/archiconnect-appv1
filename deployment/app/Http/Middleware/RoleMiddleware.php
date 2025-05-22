<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check() || !Auth::user()->hasRole($role)) {
            // Redirect them to the home page or show an unauthorized error
            // For now, let's abort with a 403 error
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
