<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Review Work Submission') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Submission Details</h3>
                        <p><strong>Job Title:</strong> {{ $workSubmission->jobAssignment->job->title }}</p>
                        <p><strong>Freelancer:</strong> {{ $workSubmission->jobAssignment->freelancer->name }}</p>
                        <p><strong>Submitted At:</strong> {{ $workSubmission->created_at->format('Y-m-d H:i') }}</p>
                        <p><strong>Status:</strong> <x-status-badge :status="$workSubmission->status" /></p>
                        @if ($workSubmission->admin_remarks)
                            <p><strong>Admin Remarks:</strong> {{ $workSubmission->admin_remarks }}</p>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Submitted Files</h3>
                        @if ($workSubmission->file_path)
                            @php
                                $filePath = Storage::url($workSubmission->file_path); // Assuming file_path is stored and accessible via Storage
                                $isImage = in_array($workSubmission->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']); // Use mime_type and add webp support for preview images.
                            @endphp
                            <div class="mt-2">
                                @if ($isImage)
                                    <img src="{{ $filePath }}" alt="{{ $workSubmission->original_filename }}" class="max-w-xs h-auto mr-2 inline-block rounded-md shadow-sm">
                                @endif
                                <a href="{{ route('admin.submissions.download', $workSubmission) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline flex items-center mt-2">
                                     <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $workSubmission->original_filename ?? 'Download Submitted File' }}
                                </a>
                                @if ($workSubmission->size)
                                ({{ \App\Helpers\FileHelper::formatBytes($workSubmission->size) }}) {{-- Assuming size is stored and FileHelper exists --}}
                                @endif
                            </div>
                        @else
                            <p class="mt-2 text-gray-600 dark:text-gray-400">No files were submitted with this submission.</p>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Associated Time Logs</h3>
                        @if ($workSubmission->timeLogs->isEmpty())
                            <p>No time logs are directly associated with this submission.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Start Time
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                End Time
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Duration
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Task Description
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($workSubmission->timeLogs as $timeLog)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $timeLog->start_time }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    {{ $timeLog->end_time ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    {{ $timeLog->duration ? gmdate('H:i:s', $timeLog->duration) : 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    {{ $timeLog->task_description ?? 'No description' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Client Review Form -->
                    @if ($workSubmission->status === 'ready_for_client_review')
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Your Review</h3>
                            <form method="POST" action="{{ route('client.work-submissions.update', $workSubmission) }}">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <x-input-label for="client_status" :value="__('Action')" />
                                    <select id="client_status" name="client_status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">Select an action</option>
                                        <option value="approved_by_client">Approve Work</option>
                                        <option value="needs_revision_by_client">Request Revision</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('client_status')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="client_remarks" :value="__('Your Remarks (Optional)')" />
                                    <x-textarea-input id="client_remarks" class="block mt-1 w-full" name="client_remarks">{{ old('client_remarks') }}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('client_remarks')" class="mt-2" />
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button class="ms-4">
                                        {{ __('Submit Review') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Review Status</h3>
                            <p>This submission is currently <strong>{{ ucfirst(str_replace('_', ' ', $workSubmission->status)) }}</strong>.</p>
                            @if ($workSubmission->client_remarks)
                                <p><strong>Your Remarks:</strong> {{ $workSubmission->client_remarks }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- Dispute Reporting Section -->
                    @if ($workSubmission->jobAssignment)
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ __('Report a Dispute') }}</h4>
                            {{-- TODO: Add logic to only show if no open dispute by this user for this assignment exists --}}
                            <a href="{{ route('job_assignments.disputes.create', $workSubmission->jobAssignment->id) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Report Dispute for this Assignment') }}
                            </a>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">If you have an issue with this job assignment or the submitted work that cannot be resolved, you can report a dispute for an admin to review.</p>
                    </div>
                    @endif

                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('client.work-submissions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Back to Submissions') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
