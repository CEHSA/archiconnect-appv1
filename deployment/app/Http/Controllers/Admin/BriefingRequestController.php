<?php

namespace App\Http\Controllers\Admin;

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
        $briefingRequests = BriefingRequest::with('client')->latest()->get();

        return view('admin.briefing-requests.index', compact('briefingRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BriefingRequest $briefingRequest)
    {
        $briefingRequest->load('client'); // Eager load the client relationship

        return view('admin.briefing-requests.show', compact('briefingRequest'));
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
    public function update(Request $request, BriefingRequest $briefingRequest)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:pending,scheduled,completed,cancelled',
        ]);

        $briefingRequest->update($validatedData);

        return redirect()->route('admin.briefing-requests.index')->with('success', 'Briefing request status updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
