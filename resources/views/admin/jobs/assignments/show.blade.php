<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Assignment Details for Job:') }} {{ $assignment->job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-6">
                        <a href="{{ route('admin.jobs.assignments.index', $assignment->job_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-2">
                            {{ __('Back to Assignments List') }}
                        </a>
                        <a href="{{ route('admin.assignments.edit', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Edit Assignment') }}
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Assignment Information') }}</h3>
                            <dl class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Job Title') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100"><a href="{{ route('admin.jobs.show', $assignment->job_id) }}" class="text-indigo-600 hover:underline">{{ $assignment->job->title }}</a></dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Freelancer') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">{{ $assignment->freelancer->name ?? 'N/A' }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Assigned By') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">{{ $assignment->assignedByAdmin->name ?? 'N/A' }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Status') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">{{ Str::title(str_replace('_', ' ', $assignment->status)) }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Date Assigned') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">{{ $assignment->created_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Last Updated') }}</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">{{ $assignment->updated_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Remarks') }}</h3>
                            <div class="mt-2 space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Admin Remarks') }}</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $assignment->admin_remarks ?: __('None') }}
                                    </p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Freelancer Remarks') }}</h4>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $assignment->freelancer_remarks ?: __('None') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Work Submissions Section -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Work Submissions') }}</h3>
                        @if ($assignment->workSubmissions->isEmpty())
                            <p class="text-gray-600 dark:text-gray-400">No work has been submitted for this assignment yet.</p>
                        @else
                            <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted At</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">File</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($assignment->workSubmissions as $submission)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $submission->title }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $submission->submitted_at->format('M d, Y H:i') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($submission->status === 'submitted') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100
                                                        @elseif($submission->status === 'under_review') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                                                        @elseif($submission->status === 'approved_by_admin') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100
                                                        @elseif($submission->status === 'needs_revision') bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100
                                                        @else bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100
                                                        @endif">
                                                        {{ Str::title(str_replace('_', ' ', $submission->status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    @if($submission->file_path)
                                                        <a href="{{ route('admin.submissions.download', $submission) }}" class="text-indigo-600 hover:underline">{{ $submission->original_filename }}</a>
                                                    @else
                                                        No file
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('admin.submissions.show', $submission->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">View/Review</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Time Logs Section -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Time Logs for this Assignment') }}</h3>
                        @if ($assignment->timeLogs->isEmpty())
                            <p class="text-gray-600 dark:text-gray-400">No time logged for this assignment yet.</p>
                        @else
                            <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Start Time</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">End Time</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Task Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($assignment->timeLogs as $log)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($log->start_time)->format('M d, Y H:i:s') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $log->end_time ? \Carbon\Carbon::parse($log->end_time)->format('M d, Y H:i:s') : 'Running...' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    @if ($log->duration)
                                                        {{ gmdate("H:i:s", $log->duration) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-pre-wrap text-sm text-gray-900 dark:text-gray-100">{{ $log->task_description ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Assignment Tasks Section -->
                    <div class="mt-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Assignment Tasks') }}</h3>
                            <a href="{{ route('admin.job-assignments.tasks.index', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Manage Tasks') }}
                            </a>
                        </div>
                        @if ($assignment->tasks->isEmpty())
                            <p class="text-gray-600 dark:text-gray-400">No tasks have been defined for this assignment yet.</p>
                        @else
                            <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($assignment->tasks as $task)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $task->order }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $task->title }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @switch($task->status)
                                                            @case('pending') bg-yellow-100 text-yellow-800 @break
                                                            @case('in_progress') bg-blue-100 text-blue-800 @break
                                                            @case('completed') bg-green-100 text-green-800 @break
                                                            @case('cancelled') bg-red-100 text-red-800 @break
                                                            @default bg-gray-100 text-gray-800
                                                        @endswitch">
                                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Task Progress History Section -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Task Progress History') }}</h3>
                        @if ($assignment->taskProgress->isEmpty())
                            <p class="text-gray-600 dark:text-gray-400">No task progress updates submitted for this assignment yet.</p>
                        @else
                            <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted At</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">File</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($assignment->taskProgress as $progress)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $progress->submitted_at->format('M d, Y H:i') }}</td>
                                                <td class="px-6 py-4 whitespace-pre-wrap text-sm text-gray-900 dark:text-gray-100">{{ $progress->description }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    @if($progress->file_path)
                                                        <a href="{{ route('freelancer.task-progress.download', $progress) }}" class="text-indigo-600 hover:underline">{{ $progress->original_filename ?? 'Download File' }}</a>
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

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
