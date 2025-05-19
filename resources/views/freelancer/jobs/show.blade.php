<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Job Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold mb-4">{{ $job->title }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Client') }}</p>
                            <p>{{ $job->user ? $job->user->name : __('Not Specified') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Status') }}</p>
                            <x-status-badge :status="$job->status" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Budget') }}</p>
                            <p>{{ $job->budget ? format_currency($job->budget) : __('Not Specified') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Hourly Rate') }}</p>
                            <p>{{ $job->hourly_rate ? format_currency($job->hourly_rate) . ' / hour' : __('Not Specified') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Not to Exceed Budget') }}</p>
                            <p>{{ $job->not_to_exceed_budget ? format_currency($job->not_to_exceed_budget) : __('Not Specified') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Posted Date') }}</p>
                            <p>{{ $job->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($job->description)) !!}
                        </div>
                    </div>

                    @if($job->skills_required)
                    <div class="mb-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Skills Required') }}</p>
                        <p>{{ $job->skills_required }}</p>
                    </div>
                    @endif
                    
                    {{-- Placeholder for Proposal Button - to be implemented with Proposal System --}}
                    @if($job->status === 'open' || $job->status === 'pending') {{-- Or other statuses where proposals are accepted --}}
                        <div class="mt-6">
                             {{-- Check if freelancer has already submitted a proposal for this job --}}
                            @php
                                $hasProposed = Auth::user()->proposals()->where('job_id', $job->id)->exists();
                            @endphp

                            @if($hasProposed)
                                <p class="text-green-600 dark:text-green-400">{{ __('You have already submitted a proposal for this job.') }}</p>
                                <a href="{{ route('freelancer.proposals.show', ['proposal' => Auth::user()->proposals()->where('job_id', $job->id)->first()->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mt-2">
                                    {{ __('View Your Proposal') }}
                                </a>
                            @else
                                {{-- Link to proposal creation form if it exists, or a placeholder message --}}
                                @if(Route::has('freelancer.proposals.store')) {{-- Assuming a route name like this for proposal creation --}}
                                    <a href="{{-- route('freelancer.proposals.create', ['job' => $job->id]) --}}" class="inline-flex items-center px-4 py-2 bg-architimex-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-architimex-primary-darker active:bg-architimex-primary-darker focus:outline-none focus:border-architimex-primary-darker focus:ring ring-architimex-primary-darker disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Submit a Proposal') }} (Coming Soon)
                                    </a>
                                @else
                                    <p class="text-blue-600 dark:text-blue-400">{{ __('Proposal submission will be available soon.') }}</p>
                                @endif
                            @endif
                        </div>
                    @endif

                    <div class="mt-8">
                        <a href="{{ route('freelancer.jobs.browse') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                            &larr; {{ __('Back to Job Listings') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
