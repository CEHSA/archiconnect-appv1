<x-layouts.admin>
    <x-slot name="title">Time Log Details</x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Time Log Details</h1>

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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600"><strong>Freelancer:</strong> {{ $timeLog->freelancer->name ?? 'N/A' }}</p>
                    <p class="text-gray-600"><strong>Job Title:</strong> {{ $timeLog->jobAssignment->job->title ?? 'N/A' }}</p>
                    <p class="text-gray-600"><strong>Assignment:</strong> {{ $timeLog->jobAssignment->title ?? 'N/A' }}</p>
                    <p class="text-gray-600"><strong>Start Time:</strong> {{ $timeLog->start_time ? $timeLog->start_time->format('Y-m-d H:i:s') : 'N/A' }}</p>
                    <p class="text-gray-600"><strong>End Time:</strong> {{ $timeLog->end_time ? $timeLog->end_time->format('Y-m-d H:i:s') : 'N/A' }}</p>
                    <p class="text-gray-600"><strong>Duration:</strong> {{ $timeLog->duration_minutes ?? 'N/A' }} minutes</p>
                    <p class="text-gray-600"><strong>Status:</strong> <x-status-badge :status="$timeLog->status" /></p>
                </div>
                <div>
                    <p class="text-gray-600"><strong>Freelancer Notes:</strong> {{ $timeLog->notes ?? 'N/A' }}</p>
                    @if ($timeLog->status !== 'pending')
                        <p class="text-gray-600"><strong>Reviewed By:</strong> {{ $timeLog->reviewedByAdmin->name ?? 'N/A' }}</p>
                        <p class="text-gray-600"><strong>Reviewed At:</strong> {{ $timeLog->reviewed_at ? $timeLog->reviewed_at->format('Y-m-d H:i:s') : 'N/A' }}</p>
                        <p class="text-gray-600"><strong>Admin Comments:</strong> {{ $timeLog->notes ?? 'N/A' }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if ($timeLog->status === 'pending')
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Review Time Log</h2>
                <form action="{{ route('admin.time-logs.review', $timeLog->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="admin_comments" class="block text-sm font-medium text-gray-700">Admin Comments (Optional)</label>
                        <textarea name="admin_comments" id="admin_comments" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                        @error('admin_comments')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Submit Review</button>
                </form>
            </div>
        @endif
    </div>
</x-layouts.admin>
