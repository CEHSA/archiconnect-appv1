<x-layouts.freelancer>
    <x-slot name="title">Edit Time Log</x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Edit Time Log</h1>

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

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('freelancer.time-logs.update', $timeLog->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label for="job_title" class="block text-sm font-medium text-gray-700">Job Title</label>
                    <input type="text" id="job_title" value="{{ $timeLog->jobAssignment->job->title ?? 'N/A' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label for="assignment_title" class="block text-sm font-medium text-gray-700">Assignment</label>
                    <input type="text" id="assignment_title" value="{{ $timeLog->jobAssignment->title ?? 'N/A' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="text" id="start_time" value="{{ $timeLog->start_time ? $timeLog->start_time->format('Y-m-d H:i:s') : 'N/A' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="text" id="end_time" value="{{ $timeLog->end_time ? $timeLog->end_time->format('Y-m-d H:i:s') : 'N/A' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                    <input type="text" id="duration_minutes" value="{{ $timeLog->duration_minutes ?? 'N/A' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes', $timeLog->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Update Time Log
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.freelancer>
