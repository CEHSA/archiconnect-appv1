<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Assignment Details for Job:') }} {{ $assignment->job?->title ?? __('Job Not Found') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        @if($assignment->job_id)
                            <a href="{{ route('admin.jobs.show', $assignment->job_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-2">
                                {{ __('Back to Job Details') }}
                            </a>
                        @else
                            <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">{{ __('Job reference missing') }}</span>
                        @endif
                        {{-- General Edit button for the assignment itself, might be moved into a specific tab or kept general --}}
                         <!-- The general Edit Assignment button is now part of the Assignment Information tab -->
                    </div>

                    <!-- Tab Navigation -->
                    <div x-data="{ activeTab: 'info' }" class="mb-6">
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="activeTab = 'info'"
                                        :class="{ 'border-teal-500 text-teal-600': activeTab === 'info', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'info' }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Assignment Information
                                </button>
                                <button @click="activeTab = 'submissions'"
                                        :class="{ 'border-teal-500 text-teal-600': activeTab === 'submissions', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'submissions' }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Work Submissions
                                </button>
                                <button @click="activeTab = 'timelogs'"
                                        :class="{ 'border-teal-500 text-teal-600': activeTab === 'timelogs', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'timelogs' }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Time Logs
                                </button>
                                <button @click="activeTab = 'tasks'"
                                        :class="{ 'border-teal-500 text-teal-600': activeTab === 'tasks', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tasks' }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Assignment Tasks
                                </button>
                                <button @click="activeTab = 'notes'"
                                        :class="{ 'border-teal-500 text-teal-600': activeTab === 'notes', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'notes' }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Admin Notes
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="mt-6">
                            <!-- Assignment Information Tab -->
                            <div x-show="activeTab === 'info'">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ __('Assignment Information') }}</h3>
                                    <a href="{{ route('admin.job-assignments.edit', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Edit Assignment Details') }}
                                    </a>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6"> <!-- Removed gap-y, using py-3 on items for vertical spacing -->
                                    <div>
                                        <dl class="mt-2 divide-y divide-gray-200">
                                            <div class="py-3">
                                                <dt class="block text-sm font-medium text-gray-500">{{ __('Job Title') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900">
                                                    @if($assignment->job && $assignment->job_id)
                                                        <a href="{{ route('admin.jobs.show', $assignment->job_id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">{{ $assignment->job->title }}</a>
                                                    @elseif($assignment->job)
                                                        {{ $assignment->job->title }}
                                                    @else
                                                        {{ __('Job Not Found') }}
                                                    @endif
                                                </dd>
                                            </div>
                                            <div class="py-3">
                                                <dt class="block text-sm font-medium text-gray-500">{{ __('Freelancer') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $assignment->freelancer->name ?? 'N/A' }}</dd>
                                            </div>
                                            <div class="py-3">
                                                <dt class="block text-sm font-medium text-gray-500">{{ __('Assigned By') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $assignment->assignedByAdmin->name ?? 'N/A' }}</dd>
                                            </div>
                                            <div class="py-3">
                                                <dt class="block text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ Str::title(str_replace('_', ' ', $assignment->status)) }}</dd>
                                            </div>
                                            <div class="py-3">
                                                <dt class="block text-sm font-medium text-gray-500">{{ __('Date Assigned') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $assignment->created_at->format('Y-m-d H:i:s') }}</dd>
                                            </div>
                                            <div class="py-3">
                                                <dt class="block text-sm font-medium text-gray-500">{{ __('Last Updated') }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $assignment->updated_at->format('Y-m-d H:i:s') }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                    <div class="mt-2 divide-y divide-gray-200"> <!-- Removed h4, added mt-2 to div -->
                                        <div class="py-3">
                                            <dt class="block text-sm font-medium text-gray-500">{{ __('Admin Remarks') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">
                                                {{ $assignment->admin_remarks ?: __('None') }}
                                            </dd>
                                        </div>
                                        <div class="py-3">
                                            <dt class="block text-sm font-medium text-gray-500">{{ __('Freelancer Remarks') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">
                                                {{ $assignment->freelancer_remarks ?: __('None') }}
                                            </dd>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Freelancer Work Submissions Tab -->
                            <div x-show="activeTab === 'submissions'">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ __('Freelancer Work Submissions') }}</h3>
                                    {{-- Add appropriate button if needed, e.g., link to a page to create a new submission if admins can do that --}}
                                </div>
                                @if ($assignment->workSubmissions->isEmpty())
                                    <p class="text-gray-600">No work has been submitted for this assignment yet.</p>
                                @else
                                    <div class="overflow-x-auto bg-white shadow sm:rounded-lg border border-gray-200">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-cyan-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Title</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Submitted At</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Status</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">File</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($assignment->workSubmissions as $submission)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $submission->title }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : ($submission->created_at ? $submission->created_at->format('M d, Y H:i') : 'N/A') }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                            <x-status-badge :status="$submission->status" />
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                            @if($submission->file_path)
                                                                <a href="{{ route('admin.work-submissions.download', $submission) }}" class="text-blue-600 hover:text-blue-700 hover:underline">{{ $submission->original_filename ?? 'Download File' }}</a>
                                                            @else
                                                                No file
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                            <a href="{{ route('admin.work-submissions.show', $submission->id) }}" class="text-blue-600 hover:text-blue-700">View/Review</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            <!-- Time Logs Tab -->
                            <div x-show="activeTab === 'timelogs'">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ __('Time Log History') }}</h3>
                                    <a href="{{ route('admin.time-logs.index', ['assignment_id' => $assignment->id]) }}" class="inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('View All Time Logs') }}
                                    </a>
                                </div>
                                @if ($assignment->timeLogs->isEmpty())
                                    <p class="text-gray-600">No time logged for this assignment yet.</p>
                                @else
                                    <div class="overflow-x-auto bg-white shadow sm:rounded-lg border border-gray-200">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-cyan-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Start Time</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">End Time</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Duration</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Task Description</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Status</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($assignment->timeLogs as $log)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->start_time ? \Carbon\Carbon::parse($log->start_time)->format('M d, Y H:i:s') : 'N/A' }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $log->end_time ? \Carbon\Carbon::parse($log->end_time)->format('M d, Y H:i:s') : 'Running...' }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            @if ($log->duration)
                                                                {{ gmdate("H\h i\m s\s", $log->duration) }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-pre-wrap text-sm text-gray-900">{{ $log->task_description ?: '-' }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                            <x-status-badge :status="$log->status" />
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                            <a href="{{ route('admin.time-logs.show', $log->id) }}" class="text-blue-600 hover:text-blue-700">View/Review</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            <!-- Assignment Tasks Tab -->
                            <div x-show="activeTab === 'tasks'">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ __('Assignment Tasks') }}</h3>
                                    <a href="{{ route('admin.job-assignments.tasks.index', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Manage Tasks') }}
                                    </a>
                                </div>
                                @if ($assignment->tasks->isEmpty())
                                    <p class="text-gray-600">No tasks have been defined for this assignment yet.</p>
                                @else
                                    <div class="overflow-x-auto bg-white shadow sm:rounded-lg border border-gray-200 mb-6">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-cyan-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Order</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Title</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Status</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Due Date</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($assignment->tasks->sortBy('order') as $task)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $task->order }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $task->title }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <x-status-badge :status="$task->status" />
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                            {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                            <a href="{{ route('admin.tasks.edit', ['task' => $task->id]) }}" class="text-blue-600 hover:text-blue-700">Edit</a>
                                                            <a href="#" class="text-red-600 hover:text-red-700 ml-2">Delete</a> <!-- Placeholder for Delete -->
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Task Progress History') }}</h4>
                                @if ($assignment->taskProgress->isEmpty())
                                    <p class="text-gray-600">No task progress updates submitted for this assignment yet.</p>
                                @else
                                    <div class="overflow-x-auto bg-white shadow sm:rounded-lg border border-gray-200">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-cyan-700">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Submitted At</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Description</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">File</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($assignment->taskProgress as $progress)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $progress->submitted_at ? $progress->submitted_at->format('M d, Y H:i') : 'N/A' }}</td>
                                                        <td class="px-6 py-4 whitespace-pre-wrap text-sm text-gray-900">{{ $progress->description }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                            @if($progress->file_path)
                                                                <a href="{{ route('freelancer.task-progress.download', $progress) }}" class="text-blue-600 hover:text-blue-700 hover:underline">{{ $progress->original_filename ?? 'Download File' }}</a>
                                                            @else
                                                                No file
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            <!-- Admin Notes Tab -->
                            <div x-show="activeTab === 'notes'">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Admin Notes') }}</h3>
                                    {{-- No specific button here, form is below --}}
                                </div>

                                @if ($assignment->assignmentNotes && $assignment->assignmentNotes->isNotEmpty())
                                    <div class="space-y-4 mb-6">
                                        @foreach($assignment->assignmentNotes as $note) {{-- Already ordered by desc in controller --}}
                                            <div class="border p-3 rounded-md dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                                                <div class="flex justify-between items-center mb-1">
                                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $note->admin->name ?? 'Admin' }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $note->created_at->format('M d, Y H:i A') }}</p>
                                                </div>
                                                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $note->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">No notes for this assignment yet.</p>
                                @endif

                                <form method="POST" action="{{ route('admin.job-assignments.notes.store', $assignment) }}" class="mt-6 border-t dark:border-gray-700 pt-6">
                                    @csrf
                                    <div>
                                        <x-input-label for="note_content" :value="__('Add New Note')" class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2" />
                                        <textarea id="note_content" name="content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" required>{{ old('content') }}</textarea>
                                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                                    </div>
                                    <div class="mt-4">
                                        <x-primary-button>
                                            {{ __('Save Note') }}
                                        </x-primary-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    @endpush
</x-admin-layout>
