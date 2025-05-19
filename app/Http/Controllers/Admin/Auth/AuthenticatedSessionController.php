<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log; // Added this line

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('admin.auth.login');
    }    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        Log::info('Admin login attempt for: ' . $request->email);

        // Attempt to authenticate the admin
        if (Auth::guard('admin')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            Log::info('Admin authentication successful for: ' . $request->email);
            $request->session()->regenerate();
            Log::info('Session regenerated. Admin user: ' . json_encode(Auth::guard('admin')->user()));
            Log::info('Redirecting to admin.dashboard.');
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        Log::warning('Admin authentication failed for: ' . $request->email);
        // Authentication failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout(); // Use the 'admin' guard

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('admin.login')); // Redirect to admin login page
    }
}
