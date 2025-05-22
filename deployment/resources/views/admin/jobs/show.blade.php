<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job Details') }}: {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-architimex-sidebar">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('admin.jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150">&larr; {{ __('Back to Jobs List') }}</a>
                    </div>

                    <!-- Tab Navigation -->
                    <div x-data="{ activeTab: 'details' }" class="mb-6">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="activeTab = 'details'" :class="{ 'border-architimex-sidebar text-architimex-sidebar': activeTab === 'details', 'border-transparent text-gray-700 hover:text-architimex-sidebar hover:border-architimex-sidebar': activeTab !== 'details' }" :style="activeTab === 'details' ? 'color: #2D5C5C !important;' : ''" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Details') }}
                                </button>
                                <button @click="activeTab = 'assignments'" :class="{ 'border-architimex-sidebar text-architimex-sidebar': activeTab === 'assignments', 'border-transparent text-gray-700 hover:text-architimex-sidebar hover:border-architimex-sidebar': activeTab !== 'assignments' }" :style="activeTab === 'assignments' ? 'color: #2D5C5C !important;' : ''" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Assignments') }}
                                </button>
                                <button @click="activeTab = 'comments'" :class="{ 'border-architimex-sidebar text-architimex-sidebar': activeTab === 'comments', 'border-transparent text-gray-700 hover:text-architimex-sidebar hover:border-architimex-sidebar': activeTab !== 'comments' }" :style="activeTab === 'comments' ? 'color: #2D5C5C !important;' : ''" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Comments') }}
                                </button>
                                <button @click="activeTab = 'work-submissions'" :class="{ 'border-architimex-sidebar text-architimex-sidebar': activeTab === 'work-submissions', 'border-transparent text-gray-700 hover:text-architimex-sidebar hover:border-architimex-sidebar': activeTab !== 'work-submissions' }" :style="activeTab === 'work-submissions' ? 'color: #2D5C5C !important;' : ''" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Work Submissions') }}
                                </button>
                                <button @click="activeTab = 'disputes'" :class="{ 'border-architimex-sidebar text-architimex-sidebar': activeTab === 'disputes', 'border-transparent text-gray-700 hover:text-architimex-sidebar hover:border-architimex-sidebar': activeTab !== 'disputes' }" :style="activeTab === 'disputes' ? 'color: #2D5C5C !important;' : ''" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Disputes') }}
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="mt-6">
                            <div x-show="activeTab === 'details'">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Job Title') }}:</strong>
                                        <p class="mt-1 text-sm text-architimex-sidebar dark:text-gray-100" style="color: #2D5C5C !important;">{{ $job->title }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Client') }}:</strong>
                                        <p class="mt-1 text-sm text-architimex-sidebar dark:text-gray-100" style="color: #2D5C5C !important;">{{ $job->user->name ?? __('N/A') }} ({{ $job->user->email ?? '' }})</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Description') }}:</strong>
                                        <div class="mt-1 text-sm text-architimex-sidebar dark:text-gray-100 whitespace-pre-wrap" style="color: #2D5C5C !important;">{!! $job->description !!}</div>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Budget') }}:</strong>
                                        <p class="mt-1 text-sm text-architimex-sidebar dark:text-gray-100" style="color: #2D5C5C !important;">R{{ number_format($job->budget, 2) }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Hourly Rate') }}:</strong>
                                        <p class="mt-1 text-sm text-architimex-sidebar dark:text-gray-100" style="color: #2D5C5C !important;">R{{ number_format($job->hourly_rate, 2) }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Not-to-Exceed Budget') }}:</strong>
                                        <p class="mt-1 text-sm text-architimex-sidebar dark:text-gray-100" style="color: #2D5C5C !important;">R{{ number_format($job->not_to_exceed_budget, 2) }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Skills Required') }}:</strong>
                                        <p class="mt-1 text-sm text-architimex-sidebar dark:text-gray-100" style="color: #2D5C5C !important;">{{ $job->skills_required ?? __('N/A') }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Status') }}:</strong>
                                        <div class="mt-1">
                                            <x-status-badge :status="$job->status" />
                                        </div>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Created By (Admin)') }}:</strong>
                                        <p class="mt-1 text-sm text-architimex-sidebar dark:text-gray-100" style="color: #2D5C5C !important;">{{ $job->createdByAdmin->name ?? __('N/A') }} ({{ $job->createdByAdmin->email ?? '' }})</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Created At') }}:</strong>
                                        <p class="mt-1 text-sm text-architimex-sidebar dark:text-gray-100" style="color: #2D5C5C !important;">{{ $job->created_at->format('F j, Y, g:i a') }}</p>
                                    </div>
                                    <div>
                                        <strong class="block text-sm font-medium text-gray-700">{{ __('Last Updated') }}:</strong>
                                        <p class="mt-1 text-sm text-architimex-sidebar dark:text-gray-100" style="color: #2D5C5C !important;">{{ $job->updated_at->format('F j, Y, g:i a') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div x-show="activeTab === 'assignments'">
                                <h4 class="text-lg font-semibold text-architimex-sidebar mb-4">{{ __('Assignments') }}</h4>
                                @if($job->assignments->isEmpty())
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">No assignments for this job yet.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($job->assignments as $assignment)
                                            <div class="border border-architimex-sidebar rounded-lg p-4">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="font-medium text-architimex-sidebar" style="color: #2D5C5C !important;">{{ $assignment->freelancer->name ?? 'N/A' }}</p>
                                                        <p class="text-sm text-gray-700">Assigned by {{ $assignment->assignedByAdmin->name ?? 'N/A' }} on {{ $assignment->created_at->format('M j, Y') }}</p>
                                                    </div>
                                                    <x-status-badge :status="$assignment->status" />
                                                </div>
                                                @if($assignment->admin_remarks)
                                                    <p class="mt-2 text-sm text-gray-700">Admin Remarks: {{ $assignment->admin_remarks }}</p>
                                                @endif
                                                @if($assignment->freelancer_remarks)
                                                    <p class="mt-2 text-sm text-gray-700">Freelancer Remarks: {{ $assignment->freelancer_remarks }}</p>
                                                @endif
                                                <div class="mt-4">
                                                    <a href="{{ route('admin.job-assignments.show', $assignment) }}" class="text-blue-600 hover:text-blue-900 hover:underline text-sm">{{ __('View Assignment Details') }} &rarr;</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="mt-6">
                                    <a href="{{ route('admin.job-assignments.create', ['job_id' => $job->id]) }}" class="inline-flex items-center px-4 py-2 bg-architimex-sidebar border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-architimex-primary-darker active:bg-architimex-primary-darker focus:outline-none focus:ring-2 focus:ring-architimex-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                        {{ __('Create New Assignment') }}
                                    </a>
                                </div>
                            </div>

                            <div x-show="activeTab === 'comments'">
                                <h4 class="text-lg font-semibold text-architimex-sidebar mb-4">{{ __('Comments') }}</h4>
                                @if($job->comments->isEmpty())
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">No comments for this job yet.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($job->comments as $comment)
                                            <div class="border border-architimex-sidebar rounded-lg p-4">
                                                <div class="flex justify-between items-center">
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $comment->user->name ?? 'N/A' }}</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $comment->created_at->format('M j, Y g:i a') }}</p>
                                                </div>
                                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $comment->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Add Comment Form -->
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <form method="POST" action="{{ route('admin.jobs.comments.store', $job) }}">
                                        @csrf
                                        <div>
                                            <x-input-label for="comment_content" :value="__('Add a Comment')" class="text-gray-700 mb-1" />
                                            <textarea id="comment_content" name="content" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 placeholder-gray-700" placeholder="{{ __('Type your comment here...') }}" required></textarea>
                                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                                        </div>

                                        <div class="mt-4">
                                            <x-primary-button>
                                                {{ __('Post Comment') }}
                                            </x-primary-button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div x-show="activeTab === 'work-submissions'">
                                <h4 class="text-lg font-semibold text-architimex-sidebar mb-4">{{ __('Work Submissions & Proposals') }}</h4>

                                <!-- Proposals Section -->
                                <div class="mb-8">
                                    <h5 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3 pt-2 border-t border-gray-200 dark:border-gray-700">{{ __('Proposals') }}</h5>
                                    @if($job->proposals->isEmpty())
                                        <p class="mt-2 text-gray-600 dark:text-gray-400">No proposals submitted for this job yet.</p>
                                    @else
                                        <div class="space-y-4">
                                            @foreach($job->proposals as $proposal)
                                                <div class="border border-architimex-sidebar rounded-lg p-4">
                                                    <div class="flex justify-between items-center">
                                                        <div>
                                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $proposal->user->name ?? 'N/A' }}</p>
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">Bid: R{{ number_format($proposal->bid_amount, 2) }}</p>
                                                        </div>
                                                        <x-status-badge :status="$proposal->status" />
                                                    </div>
                                                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($proposal->proposal_text, 200) }}</p>
                                                    <div class="mt-4">
                                                        <a href="{{ route('admin.proposals.show', $proposal) }}" class="text-blue-600 hover:text-blue-900 hover:underline text-sm">{{ __('View Proposal Details') }} &rarr;</a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Work Submissions Section -->
                                <div>
                                    <h5 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3 pt-4 border-t border-gray-200 dark:border-gray-700">{{ __('Work Submissions') }}</h5>
                                    @php
                                        $allSubmissions = $job->assignments->flatMap(fn($assignment) => $assignment->workSubmissions);
                                    @endphp
                                    @if($allSubmissions->isEmpty())
                                        <p class="mt-2 text-gray-600 dark:text-gray-400">No work submissions for this job yet.</p>
                                    @else
                                        <div class="space-y-4">
                                            @foreach($allSubmissions as $submission)
                                                <div class="border border-architimex-sidebar rounded-lg p-4">
                                                    <div class="flex justify-between items-center">
                                                        <div>
                                                            <p class="font-medium text-gray-900 dark:text-gray-100">Submission from {{ $submission->freelancer->name ?? 'N/A' }}</p>
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">Submitted on {{ $submission->created_at->format('M j, Y g:i a') }}</p>
                                                        </div>
                                                        <x-status-badge :status="$submission->status" />
                                                    </div>
                                                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($submission->description, 200) }}</p>
                                                    <div class="mt-4">
                                                         <a href="{{ route('admin.work-submissions.show', $submission) }}" class="text-blue-600 hover:text-blue-900 hover:underline text-sm">{{ __('View Submission Details') }} &rarr;</a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                             <div x-show="activeTab === 'disputes'">
                                <h4 class="text-lg font-semibold text-architimex-sidebar mb-4">{{ __('Disputes') }}</h4>
                                @php
                                    $allDisputes = $job->assignments->flatMap(fn($assignment) => $assignment->disputes);
                                @endphp
                                @if($allDisputes->isEmpty())
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">No disputes for this job yet.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($allDisputes as $dispute)
                                            <div class="border border-architimex-sidebar rounded-lg p-4">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">Dispute on Assignment for {{ $dispute->jobAssignment->freelancer->name ?? 'N/A' }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">Reported by {{ $dispute->reporter->name ?? 'N/A' }} on {{ $dispute->created_at->format('M j, Y g:i a') }}</p>
                                                    </div>
                                                    <x-status-badge :status="$dispute->status" />
                                                </div>
                                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($dispute->reason, 200) }}</p>
                                                <div class="mt-4">
                                                     <a href="{{ route('admin.disputes.show', $dispute) }}" class="text-blue-600 hover:text-blue-900 hover:underline text-sm">{{ __('View Dispute Details') }} &rarr;</a>
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
