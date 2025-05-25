<x-layouts.freelancer>
    <x-slot name="title">My Time Logs</x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">My Time Logs</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Time Tracker</h2>

            @if ($runningTimeLog)
                <div class="mb-4 p-4 border border-blue-300 rounded-lg bg-blue-50">
                    <p class="text-lg font-medium text-blue-800">Currently Tracking: <span class="font-bold">{{ $runningTimeLog->jobAssignment->job->title ?? 'N/A' }} - {{ $runningTimeLog->jobAssignment->title ?? 'N/A' }}</span></p>
                    <p class="text-blue-700">Started: {{ $runningTimeLog->start_time->format('Y-m-d H:i:s') }}</p>
                    <p class="text-blue-700 text-2xl font-bold mt-2">Elapsed Time: <span id="elapsed-time">00:00:00</span></p>

                    <form action="{{ route('freelancer.time-logs.stop', $runningTimeLog->id) }}" method="POST" class="mt-4" id="stop-timer-form">
                        @csrf
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                            <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="proof_of_work" class="block text-sm font-medium text-gray-700">Proof of Work (optional)</label>
                            <input type="file" name="proof_of_work" id="proof_of_work" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">Stop Timer</button>
                    </form>
                </div>
            @else
                <form action="{{ route('freelancer.assignment-tasks.time-logs.start', ['task' => ':assignment_task_id']) }}" method="POST" id="start-timer-form">
                    @csrf
                    <div class="mb-4">
                        <label for="assignment_id" class="block text-sm font-medium text-gray-700">Select Assignment</label>
                        <select name="assignment_id" id="assignment_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">-- Select an Assignment --</option>
                            @foreach ($activeAssignments as $assignment)
                                <option value="{{ $assignment->id }}">{{ $assignment->job->title ?? 'N/A' }} - {{ $assignment->title ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                        @error('assignment_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Start Timer</button>
                </form>
            @endif
        </div>

        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignment</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration (minutes)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($timeLogs as $timeLog)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $timeLog->jobAssignment->job->title ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $timeLog->jobAssignment->title ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $timeLog->start_time ? $timeLog->start_time->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $timeLog->end_time ? $timeLog->end_time->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $timeLog->duration_minutes ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <x-status-badge :status="$timeLog->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if ($timeLog->status === \App\Models\FreelancerTimeLog::STATUS_RUNNING)
                                    <form action="{{ route('freelancer.time-logs.stop', $timeLog->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 mr-3">Stop</button>
                                    </form>
                                @elseif ($timeLog->status === \App\Models\FreelancerTimeLog::STATUS_PENDING_REVIEW || $timeLog->status === \App\Models\FreelancerTimeLog::STATUS_DECLINED)
                                    <a href="{{ route('freelancer.time-logs.edit', $timeLog->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('freelancer.time-logs.destroy', $timeLog->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No time logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $timeLogs->links() }}
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/freelancer/time-tracking.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startTimerForm = document.getElementById('start-timer-form');
            const assignmentSelect = document.getElementById('assignment_id');
            const elapsedTimeSpan = document.getElementById('elapsed-time');
            const stopTimerForm = document.getElementById('stop-timer-form');

            // --- Start Timer Logic ---
            if (startTimerForm && assignmentSelect) {
                startTimerForm.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent default form submission

                    const selectedAssignmentId = assignmentSelect.value;
                    if (!selectedAssignmentId) {
                        alert('Please select an assignment to start the timer.');
                        return;
                    }

                    if (navigator.onLine) {
                        // If online, submit the form normally to the backend
                        let actionUrl = startTimerForm.action;
                        actionUrl = actionUrl.replace(':assignment_task_id', selectedAssignmentId);
                        startTimerForm.action = actionUrl;
                        startTimerForm.submit();
                    } else {
                        // If offline, use offline timer logic
                        const newLog = TimeTracker.startOfflineTimer(selectedAssignmentId);
                        alert('Timer started offline for assignment: ' + selectedAssignmentId + '. It will sync when you are back online.');
                        // Optionally, update UI to show running offline timer
                        location.reload(); // Reload to reflect the running timer from local storage
                    }
                });
            }

            // --- Stop Timer Logic ---
            if (stopTimerForm) {
                stopTimerForm.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent default form submission

                    const notes = document.getElementById('notes').value;
                    const proofOfWorkInput = document.getElementById('proof_of_work');
                    const proofOfWorkFile = proofOfWorkInput.files[0]; // Get the file object

                    if (navigator.onLine) {
                        // If online, submit the form normally to the backend
                        stopTimerForm.submit();
                    } else {
                        // If offline, use offline timer logic
                        // Note: Handling file uploads offline is complex (e.g., Base64 encoding)
                        // For simplicity, proof of work is not fully handled in offline mode here.
                        // You might want to store a reference and prompt user to upload later.
                        const stoppedLog = TimeTracker.stopOfflineTimer(notes, null); // proofOfWorkFile is not handled here
                        alert('Timer stopped offline. Log will sync when you are back online.');
                        // Optionally, update UI to reflect stopped timer
                        location.reload(); // Reload to reflect the stopped timer from local storage
                    }
                });
            }


            // --- Timer display logic ---
            let timerInterval;
            // Get running timer from backend (if online) or local storage (if offline)
            const runningTimeLogElement = @json($runningTimeLog);
            let currentRunningLog = runningTimeLogElement || TimeTracker.getRunningOfflineTimer();

            function updateElapsedTime() {
                if (currentRunningLog && currentRunningLog.start_time) {
                    const startTime = new Date(currentRunningLog.start_time);
                    const now = new Date();
                    const elapsedMilliseconds = now - startTime;

                    const hours = Math.floor(elapsedMilliseconds / (1000 * 60 * 60));
                    const minutes = Math.floor((elapsedMilliseconds % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((elapsedMilliseconds % (1000 * 60)) / 1000);

                    const formattedTime =
                        String(hours).padStart(2, '0') + ':' +
                        String(minutes).padStart(2, '0') + ':' +
                        String(seconds).padStart(2, '0');

                    if (elapsedTimeSpan) {
                        elapsedTimeSpan.textContent = formattedTime;
                    }
                }
            }

            if (currentRunningLog) {
                updateElapsedTime(); // Initial update
                timerInterval = setInterval(updateElapsedTime, 1000); // Update every second
            }

            // Clear interval when leaving the page (optional, but good practice)
            window.addEventListener('beforeunload', function() {
                if (timerInterval) {
                    clearInterval(timerInterval);
                }
            });
        });
    </script>
    @endpush
</x-layouts.freelancer>
