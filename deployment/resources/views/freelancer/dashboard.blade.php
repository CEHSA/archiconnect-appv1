<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Freelancer Dashboard') }}
        </h2>
    </x-slot>

    <!-- Freelancer Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border border-green-300">
            <div class="p-3 bg-purple-500 bg-opacity-20 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 13h4m-4-4h4" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Active Assignments</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $activeAssignmentsCount ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border border-green-300">
            <div class="p-3 bg-red-500 bg-opacity-20 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg> {{-- Changed icon to represent proposals --}}
            </div>
            <div>
                <p class="text-gray-500 text-sm">Pending Proposals</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $pendingProposalsCount ?? 0 }}</p>
            </div>
        </div>
         <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border border-green-300">
            <div class="p-3 bg-teal-500 bg-opacity-20 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Tasks Due Soon</p> {{-- Still a placeholder value from controller --}}
                <p class="text-2xl font-semibold text-gray-800">{{ $tasksDueSoonCount ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        <!-- New Job Opportunities -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-green-300">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">New Job Opportunities</h3>
            <div class="space-y-4">
                 @forelse ($newJobOpportunities as $job)
                    <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                            <div>
                                <p class="font-medium text-gray-700">{{ $job->title }}</p>
                                <p class="text-xs text-gray-500">Client: {{ $job->client->name ?? 'N/A' }}</p>
                            </div>
                            <div class="mt-2 sm:mt-0">
                                <a href="{{ route('freelancer.jobs.show', $job->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition ease-in-out duration-150">
                                    View Job
                                </a>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Status: <x-status-badge :status="$job->status" /></p>
                    </div>
                @empty
                    <p class="text-gray-500">No new job opportunities available at the moment.</p>
                @endforelse
            </div>
        </div>

        <!-- My Active Jobs -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-green-300">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">My Active Jobs</h3>
            <div class="space-y-4">
                 @forelse ($activeJobAssignments as $assignment)
                    <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                            <div>
                                <p class="font-medium text-gray-700">{{ $assignment->job->title }}</p>
                                <p class="text-xs text-gray-500">Client: {{ $assignment->job->client->name ?? 'N/A' }}</p>
                            </div>
                            <div class="mt-2 sm:mt-0">
                                <a href="{{ route('freelancer.assignments.show', $assignment->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition ease-in-out duration-150">
                                    View Details / Comments
                                </a>
                                {{-- <button type="button" class="ml-2 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                                    Comments
                                </button> --}}
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Status: <x-status-badge :status="$assignment->derived_status" /></p>
                        {{-- You can add more details like deadline if available on $assignment->job or $assignment --}}
                    </div>
                @empty
                    <p class="text-gray-500">You have no active jobs assigned.</p>
                @endforelse
            </div>
        </div>

        <!-- My Completed Jobs -->
        <div class="bg-white p-6 rounded-xl shadow-lg border border-green-300">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">My Completed Jobs</h3>
            <div class="space-y-4">
                 @forelse ($completedJobAssignments as $assignment)
                    <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center">
                            <div>
                                <p class="font-medium text-gray-700">{{ $assignment->job->title }}</p>
                                <p class="text-xs text-gray-500">Client: {{ $assignment->job->client->name ?? 'N/A' }}</p>
                            </div>
                             <div class="mt-2 sm:mt-0">
                                <a href="{{ route('freelancer.assignments.show', $assignment->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition ease-in-out duration-150">
                                    View Details
                                </a>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Status: <x-status-badge :status="$assignment->derived_status" /></p>
                    </div>
                @empty
                    <p class="text-gray-500">You have no completed jobs.</p>
                @endforelse
            </div>
        </div>
    </div>

</x-layouts.freelancer>
