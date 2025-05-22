<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job Details') }}
        </h2>
    </x-slot>

    {{-- The main content area for the job details will be directly inside the layout's $slot --}}
    {{-- Removed the py-12 and max-w-7xl containers as the freelancer layout likely handles this --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
        <div class="p-6 text-gray-900">
            <h3 class="text-2xl font-semibold mb-4 text-gray-900">{{ $job->title }}</h3>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Client') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $job->user ? $job->user->name : __('Not Specified') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900"><x-status-badge :status="$job->status" /></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Budget') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $job->budget ? format_currency($job->budget) : __('Not Specified') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Hourly Rate') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $job->hourly_rate ? format_currency($job->hourly_rate) . ' / hour' : __('Not Specified') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Not to Exceed Budget') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $job->not_to_exceed_budget ? format_currency($job->not_to_exceed_budget) : __('Not Specified') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Posted Date') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $job->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>

            <div class="mb-6">
                <p class="text-sm font-medium text-gray-500">{{ __('Description') }}</p>
                <div class="prose max-w-none mt-1 text-sm text-gray-900">
                    {!! $job->description !!}
                </div>
            </div>

            @if($job->skills_required)
            <div class="mb-6">
                <p class="text-sm font-medium text-gray-500">{{ __('Skills Required') }}</p>
                <p class="mt-1 text-sm text-gray-900">{{ $job->skills_required }}</p>
            </div>
            @endif

            @if($job->status === 'open')
                <div class="mt-6 mb-4"> {{-- Added mb-4 for spacing --}}
                    <form method="POST" action="{{ route('freelancer.jobs.accept', $job) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Accept Job') }}
                        </button>
                    </form>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Note: Accepting this job will request admin approval. Ensure you are ready to begin.') }}</p>
                </div>
            @elseif($job->status === 'pending_admin_approval' && $job->assignments()->where('freelancer_id', Auth::id())->where('status', 'pending_admin_approval')->exists())
                <div class="mt-6 mb-4">
                    <p class="text-orange-600 font-semibold">{{ __('Job acceptance request sent. Awaiting admin approval.') }}</p>
                </div>
            @endif

            {{-- Job Application Button --}}
            @if ($job->status === 'open' && $jobPosting) {{-- $jobPosting should be passed from controller --}}
                @php
                    $existingApplication = Auth::user()->jobApplications()->where('job_posting_id', $jobPosting->id)->first();
                @endphp
                <div class="mt-6 mb-4">
                    @if ($existingApplication)
                        <p class="text-green-600 font-semibold">{{ __('You have already applied for this job posting.') }}</p>
                        {{-- Optionally, link to view/edit application:
                        <a href="{{ route('freelancer.applications.show', $existingApplication->id) }}" class="text-sm text-blue-600 hover:underline">View your application</a>
                        --}}
                    @else
                        <a href="{{ route('freelancer.job-applications.create', ['job_posting_id' => $jobPosting->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Apply for this Job') }}
                        </a>
                        <p class="mt-1 text-sm text-gray-600">{{ __('This job was specifically posted to you. Click to submit your application.') }}</p>
                    @endif
                </div>
            @endif

            <div class="mt-6 mb-4"> {{-- Container for "Have Questions?" button --}}
                <a href="{{ route('freelancer.jobs.message-admin.create', ['job' => $job->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-300 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Have Questions?') }}
                </a>
                <p class="mt-1 text-sm text-gray-600">{{ __('Contact an admin regarding this job.') }}</p>
            </div>

            {{-- Placeholder for Proposal Button - to be implemented with Proposal System --}}
            @if($job->status === 'open' || $job->status === 'pending') {{-- Or other statuses where proposals are accepted --}}
                <div class="mt-6">
                     {{-- Check if freelancer has already submitted a proposal for this job --}}
                    @php
                        $hasProposed = Auth::user()->proposals()->where('job_id', $job->id)->exists();
                    @endphp

                    @if($hasProposed)
                        <p class="text-green-600">{{ __('You have already submitted a proposal for this job.') }}</p>
                        <a href="{{ route('freelancer.proposals.show', ['proposal' => Auth::user()->proposals()->where('job_id', $job->id)->first()->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-300 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mt-2">
                            {{ __('View Your Proposal') }}
                        </a>
                    @else
                        {{-- Link to proposal creation form if it exists, or a placeholder message --}}
                        @if(Route::has('freelancer.proposals.store')) {{-- Assuming a route name like this for proposal creation --}}
                            <a href="{{-- route('freelancer.proposals.create', ['job' => $job->id]) --}}" class="inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Submit a Proposal') }} (Coming Soon)
                            </a>
                        @else
                            <p class="text-blue-600">{{ __('Proposal submission will be available soon.') }}</p>
                        @endif
                    @endif
                </div>
            @endif

            <div class="mt-8">
                <a href="{{ route('freelancer.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    &larr; {{ __('Back to Dashboard') }}
                </a>
            </div>

        </div>
    </div>
</x-layouts.freelancer>
