<x-freelancer-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job Assignment Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <a href="{{ route('freelancer.assignments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Back to My Assignments') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="text-2xl font-semibold mb-2">{{ $assignment->job->title }}</h3>
                    <p class="text-sm text-gray-600 mb-4">Assigned by: {{ $assignment->assignedByAdmin->name ?? 'N/A' }} on {{ $assignment->created_at->format('F j, Y') }}</p>

                    <!-- Tab Navigation -->
                    <div x-data="{ activeTab: 'details' }" class="mb-6">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="activeTab = 'details'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'details', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'details' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Details') }}
                                </button>
                                <button @click="activeTab = 'work-submissions'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'work-submissions', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'work-submissions' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Work Submissions') }}
                                </button>
                                <button @click="activeTab = 'time-logs'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'time-logs', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'time-logs' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Time Logs') }}
                                </button>
                                <button @click="activeTab = 'task-progress'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'task-progress', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'task-progress' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Task Progress') }}
                                </button>
                                <button @click="activeTab = 'messages'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'messages', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'messages' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Messages') }}
                                </button>
                                <button @click="activeTab = 'disputes'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'disputes', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'disputes' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Disputes') }}
                                </button>
                                <button @click="activeTab = 'budget-appeals'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'budget-appeals', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'budget-appeals' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Budget Appeals') }}
                                </button>
                                <button @click="activeTab = 'tasks'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'tasks', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tasks' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ __('Tasks') }}
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="mt-6">
                            <div x-show="activeTab === 'details'">
                                <div class="mb-6 p-4 border border-gray-200 rounded-md">
                                    <h4 class="font-semibold text-lg mb-2">{{ __('Job Description') }}</h4>
                                    <p class="whitespace-pre-wrap">{{ $assignment->job->description }}</p>
                                </div>

                                @if($assignment->admin_remarks)
                                <div class="mb-6 p-4 border border-gray-200 rounded-md bg-yellow-50">
                                    <h4 class="font-semibold text-lg mb-2">{{ __('Admin Remarks') }}</h4>
                                    <p class="whitespace-pre-wrap">{{ $assignment->admin_remarks }}</p>
                                </div>
                                @endif

                                <div class="mb-6">
                                    <h4 class="font-semibold text-lg mb-2">{{ __('Current Status') }}</h4>
                                    <x-status-badge :status="$assignment->status" />
                                </div>

                                @if ($assignment->status === 'pending_freelancer_acceptance')
                                    <div class="mt-6 p-4 border border-gray-200 rounded-md">
                                        <h4 class="font-semibold text-lg mb-3">{{ __('Respond to Assignment') }}</h4>
                                        <form method="POST" action="{{ route('freelancer.assignments.update-status', $assignment) }}">
                                            @csrf
                                            @method('PATCH')

                                            <div class="mb-4">
                                                <x-input-label for="freelancer_remarks" :value="__('Your Remarks (Optional, e.g., if declining)')" />
                                                <textarea id="freelancer_remarks" name="freelancer_remarks" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('freelancer_remarks', $assignment->freelancer_remarks) }}</textarea>
                                                <x-input-error :messages="$errors->get('freelancer_remarks')" class="mt-2" />
                                            </div>

                                            <div class="flex items-center space-x-4">
                                                <x-primary-button type="submit" name="status" value="accepted" class="bg-green-600 hover:bg-green-500 focus:bg-green-700 focus:ring-green-500">
                                                    {{ __('Accept Assignment') }}
                                                </x-primary-button>
                                                <x-danger-button type="submit" name="status" value="declined">
                                                    {{ __('Decline Assignment') }}
                                                </x-danger-button>
                                            </div>
                                             <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                        </form>
                                    </div>
                                @elseif($assignment->freelancer_remarks)
                                    <div class="mt-6 p-4 border border-gray-200 rounded-md bg-blue-50">
                                        <h4 class="font-semibold text-lg mb-2">{{ __('Your Remarks') }}</h4>
                                        <p class="whitespace-pre-wrap">{{ $assignment->freelancer_remarks }}</p>
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'work-submissions'">
                                <h4 class="font-semibold text-lg mb-4">{{ __('Work Submissions') }}</h4>
                                @if (in_array($assignment->status, ['accepted', 'in_progress', 'revision_requested']))
                                <div class="mb-4">
                                    <a href="{{ route('freelancer.assignments.submissions.create', $assignment->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Submit New Work') }}
                                    </a>
                                </div>
                                @endif

                                @if ($assignment->workSubmissions->isEmpty())
                                    <p class="text-gray-600">No work submitted for this assignment yet.</p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($assignment->workSubmissions as $submission)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $submission->title }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->submitted_at->format('M d, Y H:i') }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                            <x-status-badge :status="$submission->status" />
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            @if($submission->file_path)
                                                                @php
                                                                    $filePath = Storage::url($submission->file_path);
                                                                    $isImage = in_array($submission->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
                                                                @endphp
                                                                @if ($isImage)
                                                                    <img src="{{ $filePath }}" alt="{{ $submission->original_filename }}" class="max-w-xs h-auto mr-2 inline-block rounded-md shadow-sm">
                                                                @endif
                                                                <a href="{{ route('freelancer.submissions.download', $submission) }}" class="text-indigo-600 hover:underline">
                                                                    {{ $submission->original_filename ?? 'Download File' }}
                                                                </a>
                                                                ({{ \App\Helpers\FileHelper::formatBytes($submission->size) }})
                                                            @else
                                                                No file
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-pre-wrap text-sm text-gray-500">{{ $submission->admin_remarks ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'time-logs'">
                                <h4 class="font-semibold text-lg mb-4">{{ __('Time Tracking') }}</h4>

                                @if ($activeTimeLog)
                                    @if ($activeTimeLog->job_assignment_id === $assignment->id)
                                        <div class="mb-4 p-3 bg-blue-100 rounded-md">
                                            <p class="text-blue-700">
                                                Timer is currently running for this assignment. Started at: {{ \Carbon\Carbon::parse($activeTimeLog->start_time)->format('M d, Y H:i:s') }}.
                                            </p>
                                            @if($activeTimeLog->task_description)
                                            <p class="text-sm text-blue-600 mt-1">Task: {{ $activeTimeLog->task_description }}</p>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('freelancer.timelogs.stop', $activeTimeLog) }}">
                                            @csrf
                                            <div class="mb-4">
                                                <x-input-label for="stop_task_description" :value="__('Update Task Description (Optional)')" />
                                                <textarea id="stop_task_description" name="task_description" rows="2" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('task_description', $activeTimeLog->task_description) }}</textarea>
                                                <x-input-error :messages="$errors->get('task_description')" class="mt-2" />
                                            </div>
                                            <x-danger-button type="submit">
                                                {{ __('Stop Timer') }}
                                            </x-danger-button>
                                        </form>
                                    @else
                                        <div class="mb-4 p-3 bg-yellow-100 rounded-md">
                                            <p class="text-yellow-700">
                                                You have an active timer running for a different assignment:
                                                <a href="{{ route('freelancer.assignments.show', $activeTimeLog->job_assignment_id) }}" class="font-semibold hover:underline">
                                                    {{ $activeTimeLog->jobAssignment->job->title ?? 'View Assignment' }}
                                                </a>.
                                                Please stop it before starting a new timer for this assignment.
                                            </p>
                                        </div>
                                    @endif
                                @else
                                     @if ($assignment->status === 'accepted' || $assignment->status === 'in_progress')
                                        <form method="POST" action="{{ route('freelancer.timelogs.start') }}">
                                            @csrf
                                            <input type="hidden" name="job_assignment_id" value="{{ $assignment->id }}">
                                            <div class="mb-4">
                                                <x-input-label for="start_task_description" :value="__('Task Description (Optional)')" />
                                                <textarea id="start_task_description" name="task_description" rows="2" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('task_description') }}</textarea>
                                                <x-input-error :messages="$errors->get('task_description')" class="mt-2" />
                                            </div>
                                            <x-primary-button type="submit" class="bg-green-600 hover:bg-green-500 focus:bg-green-700 focus:ring-green-500">
                                                {{ __('Start Timer') }}
                                            </x-primary-button>
                                        </form>
                                    @else
                                        <p class="text-gray-600">You must accept this assignment to start tracking time.</p>
                                    @endif
                                @endif

                                <h5 class="font-semibold text-md mt-8 mb-4">{{ __('Time Log History for this Assignment') }}</h5>
                                @if ($assignmentTimeLogs->isEmpty())
                                    <p class="text-gray-600">No time logged for this assignment yet.</p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Description</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($assignmentTimeLogs as $log)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($log->start_time)->format('M d, Y H:i:s') }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ $log->end_time ? \Carbon\Carbon::parse($log->end_time)->format('M d, Y H:i:s') : 'Running...' }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            @if ($log->duration)
                                                                {{ gmdate("H:i:s", $log->duration) }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-pre-wrap text-sm text-gray-900">{{ $log->task_description ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'task-progress'">
                                <h4 class="font-semibold text-lg mb-4">{{ __('Task Progress Updates') }}</h4>
                                @if (in_array($assignment->status, ['accepted', 'in_progress', 'revision_requested']))
                                <div class="mb-4">
                                    <a href="{{ route('freelancer.assignments.progress.create', $assignment->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Log New Progress') }}
                                    </a>
                                </div>
                                @endif

                                @if ($assignment->taskProgress->isEmpty())
                                    <p class="text-gray-600">No task progress updates submitted for this assignment yet.</p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($assignment->taskProgress as $progress)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $progress->submitted_at->format('M d, Y H:i') }}</td>
                                                        <td class="px-6 py-4 whitespace-pre-wrap text-sm text-gray-900">{{ $progress->description }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            @if($progress->file_path)
                                                                @php
                                                                    $filePath = Storage::url($progress->file_path);
                                                                    $isImage = in_array($progress->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
                                                                @endphp
                                                                @if ($isImage)
                                                                    <img src="{{ $filePath }}" alt="{{ $progress->original_filename }}" class="max-w-xs h-auto mr-2 inline-block rounded-md shadow-sm">
                                                                @endif
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

                            <div x-show="activeTab === 'messages'">
                                <h4 class="font-semibold text-lg mb-4">{{ __('Messages to Admin') }}</h4>
                                @if (in_array($assignment->status, ['accepted', 'in_progress', 'revision_requested']))
                                <div class="mb-4">
                                    <a href="{{ route('freelancer.assignments.messages.create', $assignment->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Send New Message') }}
                                    </a>
                                </div>
                                @endif

                                @if ($assignment->conversations->isEmpty() || $assignment->conversations->first()->messages->isEmpty())
                                    <p class="text-gray-600">No messages for this assignment yet.</p>
                                @else
                                    <div class="space-y-4 max-h-96 overflow-y-auto p-2">
                                        @foreach($assignment->conversations->first()->messages as $message)
                                            <div class="flex {{ $message->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                                                <div class="max-w-3/4 {{ $message->user_id === Auth::id() ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-lg p-3 shadow">
                                                    <div class="flex justify-between items-center mb-1">
                                                        <span class="font-medium text-sm">{{ $message->user->name }}</span>
                                                        <span class="text-xs opacity-75">{{ $message->created_at->format('M d, H:i') }}</span>
                                                    </div>
                                                    <p class="text-sm">{{ $message->content }}</p>
                                                    @if($message->attachments && $message->attachments->count() > 0)
                                                        <div class="mt-2">
                                                            <div class="text-xs font-semibold">Attachments:</div>
                                                            <div class="flex flex-wrap mt-1 gap-2">
                                                                @foreach($message->attachments as $attachment)
                                                                    @php
                                                                        $filePath = Storage::url($attachment->file_path);
                                                                        $isImage = in_array(Storage::mimeType($attachment->file_path), ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
                                                                    @endphp
                                                                    <div class="flex flex-col items-center">
                                                                        @if ($isImage)
                                                                            <img src="{{ $filePath }}" alt="{{ $attachment->original_filename }}" class="max-w-xs h-auto rounded-md shadow-sm mb-1">
                                                                        @endif
                                                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="inline-flex items-center px-2 py-1 text-xs text-gray-900 bg-gray-200 rounded hover:bg-gray-300 transition">
                                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                                            </svg>
                                                                            {{ Str::limit($attachment->original_filename ?? 'Download File', 15) }}
                                                                        </a>
                                                                        @if (Storage::exists($attachment->file_path))
                                                                        <span class="text-xs text-gray-500">({{ \App\Helpers\FileHelper::formatBytes(Storage::size($attachment->file_path)) }})</span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'disputes'">
                                <h4 class="font-semibold text-lg mb-4">{{ __('Disputes') }}</h4>
                                @if (in_array($assignment->status, ['accepted', 'in_progress', 'completed', 'revision_requested']))
                                <div class="mb-4">
                                    {{-- TODO: Add logic to only show if no open dispute by this user for this assignment exists --}}
                                    <a href="{{ route('job_assignments.disputes.create', $assignment->id) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Report Dispute') }}
                                    </a>
                                </div>
                                @endif

                                @if($assignment->disputes->isEmpty())
                                    <p class="text-gray-600">No disputes for this assignment yet.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($assignment->disputes as $dispute)
                                            <div class="border rounded-lg p-4">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="font-medium text-gray-900">Reported by {{ $dispute->reporter->name ?? 'N/A' }}</p>
                                                        <p class="text-sm text-gray-600">Reported on {{ $dispute->created_at->format('M j, Y g:i a') }}</p>
                                                    </div>
                                                    <x-status-badge :status="$dispute->status" />
                                                </div>
                                                <p class="mt-2 text-sm text-gray-700">Reason: {{ $dispute->reason }}</p>
                                                @if($dispute->admin_remarks)
                                                    <p class="mt-2 text-sm text-gray-700">Admin Remarks: {{ $dispute->admin_remarks }}</p>
                                                @endif
                                                <div class="mt-4">
                                                     <a href="{{ route('admin.disputes.show', $dispute) }}" class="text-indigo-600 hover:underline text-sm">{{ __('View Dispute Details (Admin Only)') }} &rarr;</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                             <div x-show="activeTab === 'budget-appeals'">
                                <h4 class="font-semibold text-lg mb-4">{{ __('Budget Appeals') }}</h4>
                                @if (in_array($assignment->status, ['accepted', 'in_progress', 'revision_requested']))
                                <div class="mb-4">
                                    {{-- TODO: Add logic to only show if no pending appeal exists --}}
                                    <a href="{{ route('freelancer.assignments.budget-appeals.create', $assignment->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Submit Budget Appeal') }}
                                    </a>
                                </div>
                                @endif

                                @if($assignment->budgetAppeals->isEmpty())
                                    <p class="text-gray-600">No budget appeals submitted for this assignment yet.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($assignment->budgetAppeals as $appeal)
                                            <div class="border rounded-lg p-4">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="font-medium text-gray-900">Requested Budget: R{{ number_format($appeal->requested_budget, 2) }}</p>
                                                        <p class="text-sm text-gray-600">Submitted on {{ $appeal->created_at->format('M j, Y g:i a') }}</p>
                                                    </div>
                                                    <x-status-badge :status="$appeal->status" />
                                                </div>
                                                <p class="mt-2 text-sm text-gray-700">Reason: {{ $appeal->reason }}</p>
                                                @if($appeal->admin_remarks)
                                                    <p class="mt-2 text-sm text-gray-700">Admin Remarks: {{ $appeal->admin_remarks }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'tasks'">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-lg">{{ __('Assignment Tasks') }}</h4>
                                    <a href="{{ route('freelancer.assignments.tasks.create', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-architimex-primary hover:bg-architimex-primary-darker text-white font-bold text-xs uppercase tracking-widest rounded">
                                        {{ __('Add New Task') }}
                                    </a>
                                </div>

                                @if($assignment->tasks->isEmpty())
                                    <p class="text-gray-600">No tasks have been added to this assignment yet.</p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                                    <th scope="col" class="relative px-6 py-3">
                                                        <span class="sr-only">Actions</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($assignment->tasks as $task)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $task->order }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $task->title }}</td>
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
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <a href="{{ route('freelancer.tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                            <form action="{{ route('freelancer.tasks.destroy', $task) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                            </form>
                                                            {{-- TODO: Quick status update --}}
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
        </div>
    </div>
</x-freelancer-layout>
