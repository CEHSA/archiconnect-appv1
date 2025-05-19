<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Project Status Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">Client Project Status Overview</h3>

                    @if ($clients->isEmpty())
                        <p class="text-gray-600">No clients found with project data.</p>
                    @else
                        <div class="mt-6 space-y-8">
                            @foreach ($clients as $client)
                                <div class="border border-gray-200 rounded-md p-6">
                                    <h4 class="font-semibold text-xl text-gray-800 mb-4">{{ $client->name }} ({{ $client->email }})</h4>

                                    {{-- Jobs Section --}}
                                    <div class="mb-6">
                                        <h5 class="font-semibold text-lg text-gray-700 mb-3">Jobs</h5>
                                        @if ($client->jobs->isEmpty())
                                            <p class="text-gray-600 text-sm">No jobs created by this client yet.</p>
                                        @else
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Freelancer</th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @foreach ($client->jobs as $job)
                                                            <tr>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                    <a href="{{ route('admin.jobs.show', $job) }}" class="text-indigo-600 hover:underline">{{ $job->title }}</a>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                    <x-status-badge :status="$job->status" />
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    @if ($job->assignments->isNotEmpty())
                                                                        {{ $job->assignments->first()->freelancer->name ?? 'N/A' }}
                                                                    @else
                                                                        Not Assigned
                                                                    @endif
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    {{ $job->created_at->format('Y-m-d') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Briefing Requests Section --}}
                                    <div>
                                        <h5 class="font-semibold text-lg text-gray-700 mb-3">Briefing Requests</h5>
                                        @if ($client->briefingRequests->isEmpty())
                                            <p class="text-gray-600 text-sm">No briefing requests submitted by this client yet.</p>
                                        @else
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preferred Date</th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preferred Time</th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        @foreach ($client->briefingRequests as $briefing)
                                                            <tr>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                    {{ $briefing->preferred_date }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    {{ $briefing->preferred_time }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                    <x-status-badge :status="$briefing->status" />
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    {{ $briefing->created_at->format('Y-m-d H:i') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
