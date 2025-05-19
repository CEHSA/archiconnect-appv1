<?php

namespace App\Http\Controllers;

use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ClientProfileController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = Auth::user();
        // Ensure clientProfile relationship is loaded or initialized if null
        if (!$user->clientProfile) {
            $user->clientProfile()->create(['user_id' => $user->id]);
            $user->load('clientProfile'); // Reload the user with the new profile
        }
        return view('client.profile.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $clientProfile = $user->clientProfile ?: $user->clientProfile()->create(['user_id' => $user->id]);


        $validatedData = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'project_preferences' => 'nullable|string',
            'contact_details' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Max 2MB
            'company_website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if it exists
            if ($clientProfile->profile_picture) {
                Storage::disk('public')->delete($clientProfile->profile_picture);
            }
            // Store new picture
            $path = $request->file('profile_picture')->store('profile_pictures/client', 'public');
            $validatedData['profile_picture'] = $path;
        }

        $clientProfile->update($validatedData);

        return redirect()->route('client.profile.create')->with('status', 'profile-updated');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClientProfile $profile): View
    {
        // Ensure the logged-in user owns this profile
        if (Auth::id() !== $profile->user_id) {
            abort(403);
        }
        $user = Auth::user();
        return view('client.profile.create', compact('user')); // Re-use create view for editing
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClientProfile $profile): RedirectResponse
    {
         // Ensure the logged-in user owns this profile
        if (Auth::id() !== $profile->user_id) {
            abort(403);
        }

        $validatedData = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'project_preferences' => 'nullable|string',
            'contact_details' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Max 2MB
            'company_website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if it exists
            if ($profile->profile_picture) {
                Storage::disk('public')->delete($profile->profile_picture);
            }
            // Store new picture
            $path = $request->file('profile_picture')->store('profile_pictures/client', 'public');
            $validatedData['profile_picture'] = $path;
        }

        $profile->update($validatedData);

        return redirect()->route('client.profile.edit', $profile)->with('status', 'profile-updated');
    }
}
