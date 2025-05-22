<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Time Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Log Details</h3>
                            <p class="mt-1 text-sm text-gray-600"><strong>Freelancer:</strong> {{ $timeLog->freelancer->name }}</p>
                            <p class="mt-1 text-sm text-gray-600"><strong>Job:</strong> {{ $timeLog->assignmentTask->jobAssignment->job->title }}</p>
                            <p class="mt-1 text-sm text-gray-600"><strong>Task:</strong> {{ $timeLog->assignmentTask->title }}</p>
                            <p class="mt-1 text-sm text-gray-600"><strong>Start Time:</strong> {{ $timeLog->start_time->format('M d, Y H:i:s') }}</p>
                            <p class="mt-1 text-sm text-gray-600"><strong>End Time:</strong> {{ $timeLog->end_time ? $timeLog->end_time->format('M d, Y H:i:s') : 'N/A' }}</p>
                            <p class="mt-1 text-sm text-gray-600"><strong>Duration:</strong> {{ $timeLog->duration_for_humans }}</p>
                            <p class="mt-1 text-sm text-gray-600"><strong>Status:</strong> 
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($timeLog->status)
                                        @case(\App\Models\TimeLog::STATUS_RUNNING) bg-blue-100 text-blue-800 @break
                                        @case(\App\Models\TimeLog::STATUS_PENDING_REVIEW) bg-yellow-100 text-yellow-800 @break
                                        @case(\App\Models\TimeLog::STATUS_APPROVED) bg-green-100 text-green-800 @break
                                        @case(\App\Models\TimeLog::STATUS_DECLINED) bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ ucfirst(str_replace('_', ' ', $timeLog->status)) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Freelancer Submission</h3>
                            <p class="mt-1 text-sm text-gray-600 font-medium">Comments:</p>
                            <p class="mt-1 text-sm text-gray-600 whitespace-pre-wrap">{{ $timeLog->freelancer_comments ?: 'No comments provided.' }}</p>
                            
                            @if($timeLog->proof_of_work_path)
                                <p class="mt-2 text-sm text-gray-600 font-medium">Proof of Work:</p>
                                <a href="{{ route('admin.time-logs.download-proof', $timeLog) }}" class="text-sm text-architimex-primary hover:text-architimex-primary-darker underline">
                                    {{ $timeLog->proof_of_work_filename }}
                                </a>
                            @else
                                <p class="mt-2 text-sm text-gray-600">No proof of work uploaded.</p>
                            @endif
                        </div>
                    </div>

                    @if($timeLog->status === \App\Models\TimeLog::STATUS_PENDING_REVIEW)
                        <hr class="my-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Admin Review</h3>
                        <form method="POST" action="{{ route('admin.time-logs.review', $timeLog) }}">
                            @csrf
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700">Decision</label>
                                <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-architimex-primary focus:border-architimex-primary sm:text-sm rounded-md">
                                    <option value="{{ \App\Models\TimeLog::STATUS_APPROVED }}">Approve</option>
                                    <option value="{{ \App\Models\TimeLog::STATUS_DECLINED }}">Decline</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="admin_comments" class="block text-sm font-medium text-gray-700">Admin Comments (Optional)</label>
                                <textarea name="admin_comments" id="admin_comments" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('admin_comments') }}</textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-architimex-primary hover:bg-architimex-primary-darker text-white font-bold py-2 px-4 rounded">
                                    Submit Review
                                </button>
                            </div>
                        </form>
                    @elseif($timeLog->reviewedByAdmin)
                        <hr class="my-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Review Details</h3>
                        <p class="mt-1 text-sm text-gray-600"><strong>Reviewed By:</strong> {{ $timeLog->reviewedByAdmin->name }}</p>
                        <p class="mt-1 text-sm text-gray-600"><strong>Reviewed At:</strong> {{ $timeLog->reviewed_at->format('M d, Y H:i:s') }}</p>
                        <p class="mt-1 text-sm text-gray-600 font-medium">Admin Comments:</p>
                        <p class="mt-1 text-sm text-gray-600 whitespace-pre-wrap">{{ $timeLog->admin_comments ?: 'No comments provided.' }}</p>
                    @endif
                    
                    <div class="mt-8">
                        <a href="{{ route('admin.time-logs.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back to Time Logs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
