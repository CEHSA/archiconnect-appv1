<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <strong class="text-gray-600 dark:text-gray-400">{{ __('Description:') }}</strong>
                        <p class="mt-1">{{ $job->description }}</p>
                    </div>                    <div class="mb-4">
                        <strong class="text-gray-600 dark:text-gray-400">{{ __('Budget:') }}</strong>
                        <p class="mt-1">{{ $job->budget ? 'R' . number_format($job->budget, 2) : 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <strong class="text-gray-600 dark:text-gray-400">{{ __('Skills Required:') }}</strong>
                        <p class="mt-1">{{ $job->skills_required ?: 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <strong class="text-gray-600 dark:text-gray-400">{{ __('Status:') }}</strong>
                        <div class="mt-1">
                            <x-status-badge :status="$job->status" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <strong class="text-gray-600 dark:text-gray-400">{{ __('Posted On:') }}</strong>
                        <p class="mt-1">{{ $job->created_at->toFormattedDateString() }}</p>
                    </div> {{-- Closes the main job details div --}}

                    <!-- Assigned Freelancer/Assignment Section -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Assigned Freelancer / Assignment') }}</h3>
                        @if($job->assignments->isNotEmpty())
                            @foreach($job->assignments as $assignment)
                                <div class="mt-2 p-4 border rounded-md dark:border-gray-700">
                                    <p><strong>Freelancer:</strong> {{ $assignment->freelancer->name }}</p>
                                    <p><strong>Assigned By:</strong> {{ $assignment->assignedByAdmin->name ?? 'N/A' }}</p>
                                    <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}</p>
                                </div>
                            @endforeach
                        @else
                            <p class="mt-2 text-gray-600 dark:text-gray-400">No freelancer has been assigned to this job yet.</p>
                        @endif
                    </div>

                    <!-- Task Progress Section -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Task Progress & Time Logs') }}</h3>
                        @if($job->assignments->isNotEmpty() && $job->assignments->first()->tasks->isNotEmpty())
                            @foreach($job->assignments as $assignment)
                                @if($assignment->tasks->isNotEmpty())
                                    <div class="mt-4">
                                        <h4 class="text-md font-medium text-gray-700 dark:text-gray-300">Freelancer: {{ $assignment->freelancer->name }}</h4>
                                        @foreach($assignment->tasks as $task)
                                            @php
                                                $totalApprovedDurationSeconds = $task->timeLogs()->where('status', \App\Models\TimeLog::STATUS_APPROVED)->sum('duration_seconds');
                                                // Assuming a simple way to estimate task completion or target hours.
                                                // This needs to be more robust, e.g., tasks having estimated hours.
                                                // For now, let's use a placeholder for total task hours, e.g., 8 hours if not defined.
                                                $estimatedTaskHours = $task->estimated_hours ?? 8; // Placeholder
                                                $estimatedTaskSeconds = $estimatedTaskHours * 3600;
                                                $progressPercentage = $estimatedTaskSeconds > 0 ? min(100, ($totalApprovedDurationSeconds / $estimatedTaskSeconds) * 100) : 0;
                                            @endphp
                                            <div class="mt-3 p-3 border rounded-md dark:border-gray-700">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $task->title }}</span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\CarbonInterval::seconds($totalApprovedDurationSeconds)->cascade()->forHumans() }} approved</span>
                                                </div>
                                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Status: {{ ucfirst(str_replace('_', ' ', $task->status)) }}</p>
                                                
                                                <!-- Optionally show individual approved logs -->
                                                <div class="mt-2">
                                                    @foreach($task->timeLogs()->where('status', \App\Models\TimeLog::STATUS_APPROVED)->orderBy('start_time')->get() as $log)
                                                        <div class="text-xs p-1 my-1 border-l-2 border-green-500 pl-2">
                                                            Logged: {{ $log->duration_for_humans }} on {{ $log->start_time->format('M d') }}
                                                            @if($log->freelancer_comments)
                                                                <p class="text-gray-500 dark:text-gray-400 italic">"{{ Str::limit($log->freelancer_comments, 50) }}"</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p class="mt-2 text-gray-600 dark:text-gray-400">No tasks or time logs available for this job yet.</p>
                        @endif
                    </div>


                    <!-- Proposals Section -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Proposals') }}</h3>
                        {{-- TODO: List proposals for this job --}}
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Proposals submitted for this job will be listed here.</p>
                    </div>

                    <!-- Work Submissions Section -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Work Submissions') }}</h3>
                        {{-- TODO: List work submissions for this job --}}
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Work submissions for this job will be listed here.</p>
                    </div>

                    <!-- Comments Section -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Comments') }}</h3>
                        {{-- TODO: Display comments for this job --}}
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Comments related to this job will be displayed here.</p>
                    </div>

                    <!-- Disputes Section -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Disputes') }}</h3>
                        {{-- TODO: List disputes for this job --}}
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Disputes related to this job will be listed here.</p>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('client.jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-2">
                            {{ __('Back to Jobs') }}
                        </a>
                        <a href="{{ route('client.jobs.edit', $job) }}" class="inline-flex items-center px-4 py-2 bg-architimex-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-architimex-primary-darker focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Edit Job') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
