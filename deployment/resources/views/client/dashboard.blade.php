{{-- resources/views/client/dashboard.blade.php --}}
{{-- Use the 'layouts.client' layout.
     Set the header slot to "Client Dashboard".
     Display summary cards: "My Open Jobs", "Pending Quotes", "Completed Jobs" (use dummy data for now).
     Display a list of "My Recent Job Requests" (use dummy data).
     Style to be consistent with the admin dashboard's look and feel.
--}}
<x-layouts.client>
    <x-slot name="header">
        {{ __('Client Dashboard') }}
    </x-slot>

    @php
        // Dummy data for client dashboard
        $clientStats = [
            'openJobs' => 2,
            'pendingQuotes' => 1,
            'completedJobs' => 5,
        ];
        $recentClientJobs = [
            ['name' => 'Kitchen Renovation Brief', 'status' => 'Awaiting Architect Assignment', 'date' => '06/28/2023'],
            ['name' => 'New Patio Design', 'status' => 'In Progress', 'date' => '06/25/2023'],
        ];
    @endphp

    <!-- Client Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border border-green-300">
            <div class="p-3 bg-blue-500 bg-opacity-20 rounded-full"> {{-- Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">My Open Jobs</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $clientStats['openJobs'] }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border border-green-300">
            <div class="p-3 bg-yellow-500 bg-opacity-20 rounded-full"> {{-- Icon --}}
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Pending Quotes</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $clientStats['pendingQuotes'] }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border border-green-300">
             <div class="p-3 bg-green-500 bg-opacity-20 rounded-full"> {{-- Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Completed Jobs</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $clientStats['completedJobs'] }}</p>
            </div>
        </div>
    </div>

    <!-- My Recent Job Requests -->
    <div class="bg-white p-6 rounded-xl shadow-lg border border-green-300">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">My Recent Job Requests</h3>
        <div class="space-y-4">
            @forelse ($recentClientJobs as $job)
                <div class="p-4 border rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-center">
                        <p class="font-medium text-gray-700">{{ $job['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $job['date'] }}</p>
                    </div>
                    <p class="text-sm text-gray-500">Status: {{ $job['status'] }}</p>
                </div>
            @empty
                <p class="text-gray-500">You have no recent job requests.</p>
                 <a href="#" class="mt-4 inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">Create New Job Request</a>
            @endforelse
        </div>
    </div>
</x-layouts.client>
