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
            'project_type' => 'required|string|max:255',
            'description' => 'required|string',
            'preferred_datetime' => 'required|date',
        ]);

        $briefingRequest = auth()->user()->briefingRequests()->create([
            'project_type' => $validatedData['project_type'],
            'description' => $validatedData['description'],
            'preferred_datetime' => $validatedData['preferred_datetime'],
            'status' => 'pending', // Default status
        ]);

        return response()->json(['message' => 'Briefing request submitted successfully!', 'briefing_request' => $briefingRequest], 201);
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
