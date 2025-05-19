<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Review Work Submission') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-6 flex justify-between items-center">
                        <a href="{{ route('admin.assignments.show', $submission->job_assignment_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Back to Assignment') }}
                        </a>
                        <a href="{{ route('admin.submissions.edit', $submission->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Update Submission Status/Remarks') }}
                        </a>
                    </div>
                     @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-100 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h3 class="text-2xl font-semibold mb-1">{{ $submission->title }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Submitted by: {{ $submission->freelancer->name }} on {{ $submission->submitted_at->format('F j, Y H:i') }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">For Job: <a href="{{ route('admin.jobs.show', $submission->jobAssignment->job->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $submission->jobAssignment->job->title }}</a></p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="md:col-span-2 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                            <h4 class="font-semibold text-lg mb-2">{{ __('Freelancer Description') }}</h4>
                            <p class="whitespace-pre-wrap">{{ $submission->description ?: 'No description provided.' }}</p>
                        </div>
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                            <h4 class="font-semibold text-lg mb-2">{{ __('Submission Status') }}</h4>
                            <x-status-badge :status="$submission->status" />

                            @if($submission->file_path)
                                <h4 class="font-semibold text-lg mt-4 mb-2">{{ __('Submitted File') }}</h4>
                                <a href="{{ route('admin.submissions.download', $submission->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-700 transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $submission->original_filename }} ({{ \App\Helpers\FileHelper::formatBytes($submission->size) }})
                                </a>
                            @else
                                <p class="mt-4 text-gray-500 dark:text-gray-400">No file submitted.</p>
                            @endif
                        </div>
                    </div>

                    @if($submission->admin_remarks)
                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md bg-yellow-50 dark:bg-yellow-900">
                        <h4 class="font-semibold text-lg mb-2">{{ __('Your Remarks (Admin)') }}</h4>
                        <p class="whitespace-pre-wrap">{{ $submission->admin_remarks }}</p>
                        @if($submission->admin)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">By: {{ $submission->admin->name }} on {{ $submission->reviewed_at ? $submission->reviewed_at->format('F j, Y H:i') : 'N/A' }}</p>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Associated Time Logs Section -->
                    <div class="mt-8">
                        <h4 class="font-semibold text-lg mb-4">{{ __('Time Logs for this Assignment (around submission date)') }}</h4>
                        @if ($assignmentTimeLogs->isEmpty())
                            <p class="text-gray-600 dark:text-gray-400">No time logged for this assignment.</p>
                        @else
                            <div class="overflow-x-auto">
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
                                        @foreach ($assignmentTimeLogs as $log)
                                            <tr class="{{ $log->start_time->gt($submission->submitted_at->subHours(24)) && $log->start_time->lt($submission->submitted_at->addHours(24)) ? 'bg-yellow-50 dark:bg-yellow-900/50' : '' }}">
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

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
