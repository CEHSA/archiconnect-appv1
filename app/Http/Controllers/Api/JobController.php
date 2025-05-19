<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::all();
        return response()->json(['data' => $jobs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {
        $validated = $request->validated();
        $job = $request->user()->jobs()->create($validated);
        
        return response()->json(['data' => $job], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        return response()->json(['data' => $job]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        $validated = $request->validated();
        $job->update($validated);
        
        return response()->json(['data' => $job]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        $job->delete();
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
