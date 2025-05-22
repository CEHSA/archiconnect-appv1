<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BriefingRequest; // Add this line

class BriefingRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $briefingRequests = auth()->user()->briefingRequests()->latest()->get();

        return view('client.briefing-requests.index', compact('briefingRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('client.briefing-requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'preferred_date' => 'required|date',
            'preferred_time' => 'required', // Consider adding a time format validation if needed
            'project_overview' => 'required|string',
        ]);

        auth()->user()->briefingRequests()->create($validatedData);

        return redirect()->route('client.briefing-requests.index')->with('success', 'Briefing request submitted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BriefingRequest $briefingRequest)
    {
        // Ensure the authenticated user owns the briefing request
        if ($briefingRequest->client_id !== auth()->id()) {
            abort(403);
        }

        return view('client.briefing-requests.show', compact('briefingRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
