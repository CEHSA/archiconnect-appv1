<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Time Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 px-4 py-2 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-cyan-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        Job
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        Task
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        Start Time
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        End Time
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        Duration
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($timeLogs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log->assignmentTask->jobAssignment->job->title ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log->assignmentTask->title ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($log->start_time)->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->end_time ? \Carbon\Carbon::parse($log->end_time)->format('Y-m-d H:i') : 'Running' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->duration_seconds ? gmdate('H:i:s', $log->duration_seconds) : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-status-badge :status="$log->status" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- Add actions like view details, edit (if pending), delete (if pending/declined) --}}
                                            {{-- Example: <a href="{{ route('freelancer.time-logs.show', $log) }}" class="text-indigo-600 hover:text-indigo-900">View</a> --}}
                                            @if($log->status === \App\Models\TimeLog::STATUS_PENDING_REVIEW || $log->status === \App\Models\TimeLog::STATUS_DECLINED)
                                                {{-- Edit form could be a modal or a separate page --}}
                                                {{-- <a href="{{ route('freelancer.time-logs.edit', $log) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</a> --}}
                                                <form action="{{ route('freelancer.time-logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this time log?');" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No time logs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $timeLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.freelancer>
