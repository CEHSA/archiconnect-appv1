<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfileUpdateRequest; // Will need to create this
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
    /**
     * Display the admin's profile form.
     */
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user('admin'),
        ]);
    }

    /**
     * Update the admin's profile information.
     */
    public function updateInformation(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user('admin')->fill($request->validated());

        if ($request->user('admin')->isDirty('email')) {
            $request->user('admin')->email_verified_at = null;
        }

        $request->user('admin')->save();

        return Redirect::route('admin.profile.edit')->with('status', 'profile-information-updated');
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user('admin')->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('admin.profile.edit')->with('status', 'password-updated');
    }

    // Note: Breeze typically includes a destroy method for deleting the user account.
    // If this is needed for admins, it can be added here.
    // For now, it's omitted as per the routes file.
}
