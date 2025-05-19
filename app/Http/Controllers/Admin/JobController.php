<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJobRequest;
use App\Http\Requests\Admin\UpdateJobRequest;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Events\AdminJobPosted;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::with(['user', 'createdByAdmin'])->latest()->paginate(10);
        return view('admin.jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = User::where('role', User::ROLE_CLIENT)->orderBy('name')->get();
        return view('admin.jobs.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['created_by_admin_id'] = Auth::id();

        $job = Job::create($validatedData);

        // Dispatch the event after the job is created
        event(new AdminJobPosted($job));

        return redirect()->route('admin.jobs.index')->with('success', 'Job created successfully and freelancers notified.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        $job->load(['user', 'createdByAdmin', 'assignments', 'proposals', 'comments']);
        return view('admin.jobs.show', compact('job'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        $clients = User::where('role', User::ROLE_CLIENT)->orderBy('name')->get();
        return view('admin.jobs.edit', compact('job', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        $validatedData = $request->validated();

        $job->update($validatedData);

        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        try {
            $job->delete();
            return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return redirect()->route('admin.jobs.index')->with('error', 'Failed to delete job. It might be associated with other records.');
        }
    }
}
