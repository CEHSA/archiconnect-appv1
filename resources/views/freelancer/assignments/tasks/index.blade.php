<x-freelancer-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tasks for Assignment: ') }} {{ $assignment->job->title }}
            </h2>
            <a href="{{ route('freelancer.assignments.tasks.create', $assignment) }}" class="bg-architimex-primary hover:bg-architimex-primary-darker text-white font-bold py-2 px-4 rounded">
                {{ __('Add New Task') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($tasks->isEmpty())
                        <p class="text-gray-500">No tasks have been added to this assignment yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timer Actions</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($tasks as $task)
                                        @php
                                            $runningLog = $task->timeLogs()->where('freelancer_id', Auth::id())->where('status', \App\Models\TimeLog::STATUS_RUNNING)->first();
                                            $pendingLogs = $task->timeLogs()->where('freelancer_id', Auth::id())->where('status', \App\Models\TimeLog::STATUS_PENDING_REVIEW)->orderBy('created_at', 'desc')->get();
                                            $approvedLogs = $task->timeLogs()->where('freelancer_id', Auth::id())->where('status', \App\Models\TimeLog::STATUS_APPROVED)->orderBy('created_at', 'desc')->get();
                                            $declinedLogs = $task->timeLogs()->where('freelancer_id', Auth::id())->where('status', \App\Models\TimeLog::STATUS_DECLINED)->orderBy('created_at', 'desc')->get();
                                        @endphp
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($runningLog)
                                                    <div class="mb-2 p-2 border border-blue-300 rounded bg-blue-50">
                                                        <p class="text-sm font-semibold text-blue-700">Timer Running... (Started: {{ $runningLog->start_time->format('H:i:s') }})</p>
                                                        <form method="POST" action="{{ route('freelancer.time-logs.stop', $runningLog) }}" enctype="multipart/form-data" class="mt-2">
                                                            @csrf
                                                            <div class="mb-2">
                                                                <label for="freelancer_comments_{{ $runningLog->id }}" class="block text-xs font-medium text-gray-700">Comments:</label>
                                                                <textarea name="freelancer_comments" id="freelancer_comments_{{ $runningLog->id }}" rows="2" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label for="proof_of_work_{{ $runningLog->id }}" class="block text-xs font-medium text-gray-700">Proof of Work (Optional):</label>
                                                                <input type="file" name="proof_of_work" id="proof_of_work_{{ $runningLog->id }}" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-architimex-primary/20 file:text-architimex-primary hover:file:bg-architimex-primary/30">
                                                            </div>
                                                            <button type="submit" class="px-3 py-1 text-xs bg-red-500 hover:bg-red-600 text-white rounded">Stop Timer</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <form method="POST" action="{{ route('freelancer.assignment-tasks.time-logs.start', $task) }}">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 text-xs bg-green-500 hover:bg-green-600 text-white rounded">Start Timer</button>
                                                    </form>
                                                @endif

                                                @if($pendingLogs->isNotEmpty())
                                                    <div class="mt-2">
                                                        <h4 class="text-xs font-semibold text-yellow-600">Pending Review:</h4>
                                                        @foreach($pendingLogs as $log)
                                                            <div class="text-xs p-1 my-1 border border-yellow-300 rounded bg-yellow-50">
                                                                {{ $log->start_time->format('M d, H:i') }} - {{ $log->end_time ? $log->end_time->format('H:i') : 'N/A' }} ({{ $log->duration_for_humans }})
                                                                <p class="text-gray-600">Comments: {{ $log->freelancer_comments ?: 'N/A' }}</p>
                                                                @if($log->proof_of_work_filename)
                                                                    <p>Proof: <a href="#" class="text-blue-500 hover:underline">{{ $log->proof_of_work_filename }}</a></p> {{-- TODO: Add download route --}}
                                                                @endif
                                                                {{-- Add edit/delete for pending logs --}}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                 @if($approvedLogs->isNotEmpty())
                                                    <div class="mt-2">
                                                        <h4 class="text-xs font-semibold text-green-600">Approved Logs:</h4>
                                                        @foreach($approvedLogs as $log)
                                                            <div class="text-xs p-1 my-1 border border-green-300 rounded bg-green-50">
                                                                {{ $log->start_time->format('M d, H:i') }} - {{ $log->end_time ? $log->end_time->format('H:i') : 'N/A' }} ({{ $log->duration_for_humans }})
                                                                <p class="text-gray-600">Freelancer: {{ $log->freelancer_comments ?: 'N/A' }}</p>
                                                                <p class="text-gray-600">Admin: {{ $log->admin_comments ?: 'N/A' }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($declinedLogs->isNotEmpty())
                                                    <div class="mt-2">
                                                        <h4 class="text-xs font-semibold text-red-600">Declined Logs:</h4>
                                                        @foreach($declinedLogs as $log)
                                                            <div class="text-xs p-1 my-1 border border-red-300 rounded bg-red-50">
                                                               {{ $log->start_time->format('M d, H:i') }} - {{ $log->end_time ? $log->end_time->format('H:i') : 'N/A' }} ({{ $log->duration_for_humans }})
                                                                <p class="text-gray-600">Freelancer: {{ $log->freelancer_comments ?: 'N/A' }}</p>
                                                                <p class="text-gray-600">Admin: {{ $log->admin_comments ?: 'N/A' }}</p>
                                                                {{-- Add delete for declined logs --}}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('freelancer.tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit Task</a>
                                                {{-- Add form for status update --}}
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
</x-freelancer-layout>
