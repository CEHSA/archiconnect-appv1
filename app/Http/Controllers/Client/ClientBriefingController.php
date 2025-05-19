<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientBriefingController extends Controller
{
    /**
     * Show the form for creating a new briefing request.
     */
    public function create()
    {
        return view('client.briefing.create');
    }

    /**
     * Store a newly created briefing request in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'preferred_datetime' => ['required', 'date'],
            'project_type' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $briefingRequest = BriefingRequest::create([
            'client_id' => auth()->id(),
            'preferred_datetime' => $validatedData['preferred_datetime'],
            'project_type' => $validatedData['project_type'],
            'description' => $validatedData['description'],
            'status' => 'pending', // Default status
        ]);

        // TODO: Dispatch event to notify admin of new briefing request

        return redirect()->route('client.briefing.create')->with('success', 'Briefing request submitted successfully! We will contact you shortly to confirm the details.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
