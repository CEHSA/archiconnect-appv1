<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Job Details') }}: {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <a href="{{ route('admin.jobs.index') }}" class="text-blue-500 hover:text-blue-700">&larr; {{ __('Back to Jobs List') }}</a>
                    </div>

                    <!-- Tab Navigation -->
                    <div x-data="{ activeTab: 'details' }" class="mb-6">
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="activeTab = 'details'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'details', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'details' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Details') }}
                                </button>
                                <button @click="activeTab = 'assignments'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'assignments', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'assignments' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Assignments') }}
                                </button>
                                <button @click="activeTab = 'proposals'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'proposals', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'proposals' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Proposals') }}
                                </button>
                                <button @click="activeTab = 'comments'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'comments', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'comments' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Comments') }}
                                </button>
                                <button @click="activeTab = 'work-submissions'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'work-submissions', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'work-submissions' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Work Submissions') }}
                                </button>
                                <button @click="activeTab = 'disputes'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'disputes', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'disputes' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Disputes') }}
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="mt-6">
                            <div x-show="activeTab === 'details'">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Job Title') }}:</strong>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $job->title }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Client') }}:</strong>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $job->user->name ?? __('N/A') }} ({{ $job->user->email ?? '' }})</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description') }}:</strong>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $job->description }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Budget') }}:</strong>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">R{{ number_format($job->budget, 2) }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Hourly Rate') }}:</strong>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">R{{ number_format($job->hourly_rate, 2) }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Not-to-Exceed Budget') }}:</strong>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">R{{ number_format($job->not_to_exceed_budget, 2) }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Skills Required') }}:</strong>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $job->skills_required ?? __('N/A') }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}:</strong>
                                        <div class="mt-1">
                                            <x-status-badge :status="$job->status" />
                                        </div>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Created By (Admin)') }}:</strong>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $job->createdByAdmin->name ?? __('N/A') }} ({{ $job->createdByAdmin->email ?? '' }})</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Created At') }}:</strong>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $job->created_at->format('F j, Y, g:i a') }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Last Updated') }}:</strong>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $job->updated_at->format('F j, Y, g:i a') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div x-show="activeTab === 'assignments'">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Assignments') }}</h4>
                                @if($job->assignments->isEmpty())
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">No assignments for this job yet.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($job->assignments as $assignment)
                                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $assignment->freelancer->name ?? 'N/A' }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">Assigned by {{ $assignment->assignedByAdmin->name ?? 'N/A' }} on {{ $assignment->created_at->format('M j, Y') }}</p>
                                                    </div>
                                                    <x-status-badge :status="$assignment->status" />
                                                </div>
                                                @if($assignment->admin_remarks)
                                                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Admin Remarks: {{ $assignment->admin_remarks }}</p>
                                                @endif
                                                @if($assignment->freelancer_remarks)
                                                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Freelancer Remarks: {{ $assignment->freelancer_remarks }}</p>
                                                @endif
                                                <div class="mt-4">
                                                    <a href="{{ route('admin.job-assignments.show', $assignment) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">{{ __('View Assignment Details') }} &rarr;</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="mt-6">
                                    <a href="{{ route('admin.job-assignments.create', ['job_id' => $job->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                        {{ __('Create New Assignment') }}
                                    </a>
                                </div>
                            </div>

                            <div x-show="activeTab === 'proposals'">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Proposals') }}</h4>
                                @if($job->proposals->isEmpty())
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">No proposals submitted for this job yet.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($job->proposals as $proposal)
                                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $proposal->user->name ?? 'N/A' }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">Bid: R{{ number_format($proposal->bid_amount, 2) }}</p>
                                                    </div>
                                                    <x-status-badge :status="$proposal->status" />
                                                </div>
                                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($proposal->proposal_text, 200) }}</p>
                                                <div class="mt-4">
                                                    <a href="{{ route('admin.proposals.show', $proposal) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">{{ __('View Proposal Details') }} &rarr;</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'comments'">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Comments') }}</h4>
                                @if($job->comments->isEmpty())
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">No comments for this job yet.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($job->comments as $comment)
                                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                                <div class="flex justify-between items-center">
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $comment->user->name ?? 'N/A' }}</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $comment->created_at->format('M j, Y g:i a') }}</p>
                                                </div>
                                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $comment->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'work-submissions'">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Work Submissions') }}</h4>
                                @php
                                    $allSubmissions = $job->assignments->flatMap(fn($assignment) => $assignment->workSubmissions);
                                @endphp
                                @if($allSubmissions->isEmpty())
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">No work submissions for this job yet.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($allSubmissions as $submission)
                                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">Submission from {{ $submission->freelancer->name ?? 'N/A' }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">Submitted on {{ $submission->created_at->format('M j, Y g:i a') }}</p>
                                                    </div>
                                                    <x-status-badge :status="$submission->status" />
                                                </div>
                                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($submission->description, 200) }}</p>
                                                <div class="mt-4">
                                                     <a href="{{ route('admin.work-submissions.show', $submission) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">{{ __('View Submission Details') }} &rarr;</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                             <div x-show="activeTab === 'disputes'">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('Disputes') }}</h4>
                                @php
                                    $allDisputes = $job->assignments->flatMap(fn($assignment) => $assignment->disputes);
                                @endphp
                                @if($allDisputes->isEmpty())
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">No disputes for this job yet.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($allDisputes as $dispute)
                                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">Dispute on Assignment for {{ $dispute->jobAssignment->freelancer->name ?? 'N/A' }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">Reported by {{ $dispute->reporter->name ?? 'N/A' }} on {{ $dispute->created_at->format('M j, Y g:i a') }}</p>
                                                    </div>
                                                    <x-status-badge :status="$dispute->status" />
                                                </div>
                                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($dispute->reason, 200) }}</p>
                                                <div class="mt-4">
                                                     <a href="{{ route('admin.disputes.show', $dispute) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">{{ __('View Dispute Details') }} &rarr;</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                         <a href="{{ route('admin.jobs.edit', $job) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Edit Job') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
