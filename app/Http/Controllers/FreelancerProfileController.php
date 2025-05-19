<?php

namespace App\Http\Controllers;

use App\Models\FreelancerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class FreelancerProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Not used for now, as profile is 1-to-1 with user
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $freelancerProfile = Auth::user()->freelancerProfile;
        return view('freelancer.profile.create', compact('freelancerProfile'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'skills' => ['nullable', 'string'],
            'portfolio_link' => ['nullable', 'url', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'bio' => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'], // Max 2MB
            'availability' => ['nullable', 'string', 'in:full-time,part-time,contract'],
            'experience_level' => ['nullable', 'string', 'in:entry,intermediate,senior,expert'],
        ]);

        $freelancerProfile = Auth::user()->freelancerProfile;

        if (!$freelancerProfile) {
            // This case should ideally not happen if profile is created on registration
            // but as a fallback, create it.
            $freelancerProfile = Auth::user()->freelancerProfile()->create(['user_id' => Auth::id()]);
        }

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if it exists
            if ($freelancerProfile->profile_picture) {
                Storage::disk('public')->delete($freelancerProfile->profile_picture);
            }
            // Store new picture
            $path = $request->file('profile_picture')->store('profile_pictures/freelancer', 'public');
            $validated['profile_picture'] = $path;
        }

        $freelancerProfile->update($validated);

        return redirect()->route('freelancer.profile.create')->with('status', 'profile-updated');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not used for now
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FreelancerProfile $profile): View
    {
        // Ensure the authenticated user owns this profile
        if ($profile->user_id !== Auth::id()) {
            abort(403);
        }
        return view('freelancer.profile.create', ['freelancerProfile' => $profile]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FreelancerProfile $profile): RedirectResponse
    {
         // Ensure the authenticated user owns this profile
        if ($profile->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'skills' => ['nullable', 'string'],
            'portfolio_link' => ['nullable', 'url', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'bio' => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'], // Max 2MB
            'availability' => ['nullable', 'string', 'in:full-time,part-time,contract'],
            'experience_level' => ['nullable', 'string', 'in:entry,intermediate,senior,expert'],
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if it exists
            if ($profile->profile_picture) {
                Storage::disk('public')->delete($profile->profile_picture);
            }
            // Store new picture
            $path = $request->file('profile_picture')->store('profile_pictures/freelancer', 'public');
            $validated['profile_picture'] = $path;
        }

        $profile->update($validated);

        return redirect()->route('freelancer.profile.edit', $profile)->with('status', 'profile-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not implemented
    }
}
